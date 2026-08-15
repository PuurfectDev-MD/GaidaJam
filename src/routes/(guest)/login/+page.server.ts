import type { PageServerLoad } from './$types';
import * as dotenv from 'dotenv';

dotenv.config();

export const load: PageServerLoad = async ({ cookies }) => {
  const clientId = process.env.HACKCLUB_CLIENT_ID;
  const redirectUri = process.env.HACKCLUB_REDIRECT_URI;

  if (!clientId || !redirectUri) {
    throw new Error('Hack Club Auth credentials missing in environment variables.');
  }

  // Set CSRF state token in cookies
  const state = crypto.randomUUID();
  cookies.set('oauth_state', state, {
    path: '/',
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    maxAge: 60 * 10,
  });

  const authUrl = new URL('https://auth.hackclub.com/oauth/authorize');
  authUrl.searchParams.set('client_id', clientId);
  authUrl.searchParams.set('redirect_uri', redirectUri);
  authUrl.searchParams.set('response_type', 'code');
  authUrl.searchParams.set('scope', 'openid email name');
  authUrl.searchParams.set('state', state);

  return {
    hackAuthUrl: authUrl.toString(),
  };
};