/*
  # Add matricula limits

  1. New Tables
    - `matricula_limite`
      - `id_limite` (uuid, primary key)
      - `id_matricula` (foreign key to matricula)
      - `limite_masculino` (integer)
      - `limite_femenino` (integer)
      - `created_at` (timestamp)
      - `updated_at` (timestamp)

  2. Security
    - Enable RLS
    - Add policies for authenticated users
*/

CREATE TABLE IF NOT EXISTS matricula_limite (
    id_limite uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    id_matricula INT REFERENCES matricula(id_matricula),
    limite_masculino INT NOT NULL DEFAULT 0,
    limite_femenino INT NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(id_matricula)
);

ALTER TABLE matricula_limite ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow read access for authenticated users" ON matricula_limite
    FOR SELECT TO authenticated USING (true);

CREATE POLICY "Allow write access for authenticated users" ON matricula_limite
    FOR ALL TO authenticated USING (true);