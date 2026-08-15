import { redirect, error } from '@sveltejs/kit';
import type { RequestHandler } from './$types';
import * as dotenv from 'dotenv';
import { db } from '../../../lib/server/db';
import { users } from '../../../lib/server/db/schema';
import { eq } from 'drizzle-orm';
dotenv.config();

export const GET: RequestHandler = async ({ url, cookies, fetch }) => {
  const code = url.searchParams.get('code');
  const state = url.searchParams.get('state');
  const storedState = cookies.get('oauth_state');
  console.log('Incoming Code:', code);

  // 1. Verify CSRF State Token
  if (state && storedState && state !== storedState) {
    throw error(400, 'Invalid OAuth state token');
  }

  if (!code) {
    throw error(400, 'Missing authorization code from Hack Club');
  }

  cookies.delete('oauth_state', { path: '/' });

  const clientId = process.env.HACKCLUB_CLIENT_ID;
  const clientSecret = process.env.HACKCLUB_CLIENT_SECRET;
  const redirectUri = process.env.HACKCLUB_REDIRECT_URI;

  const tokenResponse = await fetch('https://auth.hackclub.com/oauth/token', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      client_id: clientId,
      client_secret: clientSecret,
      redirect_uri: redirectUri,
      code: code,
      grant_type: 'authorization_code',
    }),
  });

  if (!tokenResponse.ok) {
    const errorData = await tokenResponse.text();
    console.error('Token Exchange Failed:', errorData);
    throw error(500, 'Failed to exchange authorization code for access token');
  }

  const tokenData = await tokenResponse.json();
  const accessToken = tokenData.access_token;

  const userResponse = await fetch('https://auth.hackclub.com/api/v1/me', {
    headers: {
      Authorization: `Bearer ${accessToken}`,
    },
  });

  if (!userResponse.ok) {
    throw error(500, 'Failed to retrieve user profile from Hack Club Auth');
  }

  const hackClubUser = await userResponse.json();
  console.log('Hack Club User Payload:', hackClubUser);

  let user = await db.query.users.findFirst({
    where: eq(users.email, hackClubUser.identity.primary_email),
  });

  if (!user) {
    const [insertedUser] = await db
      .insert(users)
      .values({
        id: crypto.randomUUID(),
        email: hackClubUser.identity.primary_email,
        name: hackClubUser.identity.first_name ?? 'Hack Clubber',
      })
      .returning();
      
    user = insertedUser;
  }
  console.log('Successfully Authenticated Hack Club User:', hackClubUser);

  cookies.set('session_user_id', user.id, {
    path: '/',
    httpOnly: true,
    sameSite: 'lax',
    secure: process.env.NODE_ENV === 'production',
    maxAge: 60 * 60 * 24 * 7 // 1 week
  });

  redirect(303, '/app');
};