/*
  # Update platos_servidos and consumo tables

  1. Changes
    - Add new columns to platos_servidos table
    - Update consumo and consumo_detalle tables structure
    - Add indexes for better performance

  2. Security
    - Enable RLS
    - Add policies for authenticated users
*/

-- Update platos_servidos table
ALTER TABLE platos_servidos
ADD COLUMN observacion TEXT,
ADD COLUMN created_by INT REFERENCES usuarios(id);

-- Update consumo table to include more details
ALTER TABLE consumo
ADD COLUMN tipo_matricula VARCHAR(20);

-- Create index on fecha column for better filtering
CREATE INDEX idx_platos_servidos_fecha ON platos_servidos(fecha);
CREATE INDEX idx_consumo_fecha ON consumo(fecha);

-- Enable RLS
ALTER TABLE platos_servidos ENABLE ROW LEVEL SECURITY;
ALTER TABLE consumo ENABLE ROW LEVEL SECURITY;
ALTER TABLE consumo_detalle ENABLE ROW LEVEL SECURITY;

-- Create policies
CREATE POLICY "Users can read their own data" ON platos_servidos
    FOR ALL TO authenticated
    USING (created_by = auth.uid());

CREATE POLICY "Users can read their own data" ON consumo
    FOR ALL TO authenticated
    USING (created_by = auth.uid());

CREATE POLICY "Users can read their own data" ON consumo_detalle
    FOR ALL TO authenticated
    USING (id_consumo IN (SELECT id_consumo FROM consumo WHERE created_by = auth.uid()));