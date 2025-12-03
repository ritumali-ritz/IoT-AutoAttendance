- Migration: drop mobile column from students table if it exists
-- Run this on your live DB if you previously added the `mobile` column.
-- Always backup your database before running migrations.

ALTER TABLE students DROP COLUMN IF EXISTS mobile;
