/*
  # Add menu product and gramaje tables

  1. New Tables
    - `gramaje`
      - `id_gramaje` (uuid, primary key)
      - `id_producto` (foreign key to producto)
      - `gramaje_por_plato` (decimal)
      - `created_at` (timestamp)
      - `updated_at` (timestamp)
    
    - `menu_producto`
      - `id_menu_producto` (uuid, primary key) 
      - `id_menu` (foreign key to menu)
      - `id_producto` (foreign key to producto)
      - `cantidad_por_plato` (decimal)
      - `created_at` (timestamp)
      - `updated_at` (timestamp)

  2. Security
    - Enable RLS on both tables
    - Add policies for authenticated users
*/

-- Create gramaje table
CREATE TABLE IF NOT EXISTS gramaje (
    id_gramaje uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    id_producto INT REFERENCES producto(id_producto),
    gramaje_por_plato DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(id_producto)
);

-- Create menu_producto table
CREATE TABLE IF NOT EXISTS menu_producto (
    id_menu_producto uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    id_menu INT REFERENCES menu(id_menu),
    id_producto INT REFERENCES producto(id_producto),
    cantidad_por_plato DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(id_menu, id_producto)
);

-- Enable RLS
ALTER TABLE gramaje ENABLE ROW LEVEL SECURITY;
ALTER TABLE menu_producto ENABLE ROW LEVEL SECURITY;

-- Create policies
CREATE POLICY "Allow read access for authenticated users" ON gramaje
    FOR SELECT TO authenticated USING (true);

CREATE POLICY "Allow write access for authenticated users" ON gramaje
    FOR ALL TO authenticated USING (true);

CREATE POLICY "Allow read access for authenticated users" ON menu_producto
    FOR SELECT TO authenticated USING (true);

CREATE POLICY "Allow write access for authenticated users" ON menu_producto
    FOR ALL TO authenticated USING (true);