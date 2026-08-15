import * as dotenv from 'dotenv';
dotenv.config();

import { createServerClient } from '@supabase/ssr';
import type { Handle } from '@sveltejs/kit';
import { db } from './lib/server/db';
import { users } from './lib/server/db/schema';
import { eq } from 'drizzle-orm';

const supabaseUrl = process.env.PUBLIC_SUPABASE_URL || '';
const supabaseKey = process.env.PUBLIC_SUPABASE_PUBLISHABLE_KEY || '';

export const handle: Handle = async ({ event, resolve }) => {
  // 1. Initialize Supabase SSR Client using process.env
  event.locals.supabase = createServerClient(supabaseUrl, supabaseKey, {
    cookies: {
      getAll: () => event.cookies.getAll(),
      setAll: (cookiesToSet) => {
        cookiesToSet.forEach(({ name, value, options }) => {
          event.cookies.set(name, value, { ...options, path: '/' });
        });
      },
    },
  });

  // 2. Read custom Hack Club Auth session cookie & attach user to event.locals
  const userId = event.cookies.get('session_user_id');

  if (userId) {
    try {
      const user = await db.query.users.findFirst({
        where: eq(users.id, userId)
      });
      if (user) {
        event.locals.user = user;
      }
    } catch (err) {
      console.error('Error fetching session user:', err);
    }
  } else {
    event.locals.user = null;
  }

  return resolve(event, {
    filterSerializedResponseHeaders(name: string) {
      return name === 'content-range' || name === 'x-supabase-api-version';
    },
  });
};