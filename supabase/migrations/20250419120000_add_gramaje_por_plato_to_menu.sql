-- Migration to add gramaje_por_plato column to menu table
ALTER TABLE menu
ADD COLUMN gramaje_por_plato DECIMAL(10,2) DEFAULT 0 NOT NULL;
