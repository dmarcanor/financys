-- create database settings (only runs on first init)
ALTER DATABASE app_db SET timezone = 'America/Caracas';

-- Ensure locale/encoding are as intended; if the template was created with the right locale this is mostly informational.
-- Create ICU collations (Postgres >= 10 with ICU support). Adjust names/locales if desired.
DO $$
BEGIN
  -- Example: spanish collation for Venezuela (ICU)
  -- Name: es_ve
  IF NOT EXISTS (SELECT 1 FROM pg_collation WHERE collname = 'es_ve') THEN
    PERFORM pg_collation_create('es_ve', 'und', 'es-VE', 'icu');
  END IF;
END$$;

-- Example of creating a schema and a table using the new collation:
CREATE SCHEMA IF NOT EXISTS app AUTHORIZATION CURRENT_USER;
CREATE TABLE IF NOT EXISTS app.example (
  id serial PRIMARY KEY,
  name text COLLATE "es_ve"
);