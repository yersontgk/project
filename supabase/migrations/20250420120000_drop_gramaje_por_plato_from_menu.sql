-- Migration to drop gramaje_por_plato column from menu table
ALTER TABLE menu
DROP COLUMN gramaje_por_plato;
