import { pgTable, serial, integer, text, uuid , boolean, timestamp} from 'drizzle-orm/pg-core';



export const users = pgTable('users', {
	id: uuid('id').primaryKey(),
	email: text('email').notNull(),
	name: text('name').notNull(),
	createdAt: timestamp('created_at').notNull().defaultNow(),
})
