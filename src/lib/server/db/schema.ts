import { pgTable, serial, integer, text, uuid , boolean, timestamp} from 'drizzle-orm/pg-core';



export const user = pgTable('user', {
	id: uuid('id').primaryKey(),
	hack_club_id: text('hack_club_id').notNull(),
	email: text('email').notNull(),
	name: text('name').notNull(),
	slack_id: text('slack_id').notNull(),
	verification_status: boolean('verification_status').notNull().default(false),
	created_at: timestamp('created_at').notNull().defaultNow(),
})
