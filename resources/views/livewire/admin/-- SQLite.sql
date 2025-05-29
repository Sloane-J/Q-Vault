-- SQLite
UPDATE papers 
SET is_visible = 1 
WHERE is_visible = 'public';

UPDATE papers 
SET is_visible = 0 
WHERE is_visible = 'restricted';