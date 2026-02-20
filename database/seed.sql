INSERT INTO users (username, password_hash) VALUES
('admin', '$2y$12$c5LElCFrsOzaT7j4Yb0ExuQWOXhb0KXH.pPdZ5UK8jPwi4zqJVWrO');

-- IMPORTANTE: Reemplaza project_url y los nombres de archivo por los reales en tu entorno.
-- doc_filename se busca dentro de /documentacion.
-- func_filename se busca dentro de /funcionalidad.
-- video_filename se busca dentro de /video.
INSERT INTO projects (name, company, project_url, doc_filename, func_filename, video_filename) VALUES
('Portal de Distribuidores Prosa Natural', 'PASS Consultores', 'https://cambiar-por-tu-url-portal.example.com', 'pass.docx', 'pass_funcionalidad.docx', 'pass.mp4'),
('Prosa Natural Web', 'PASS Labs', 'https://cambiar-por-tu-url-web.example.com', 'pass.docx', 'pass_funcionalidad.docx', 'pass.mp4'),
('Pass', 'PASS Digital', 'https://cambiar-por-tu-url-pass.example.com', 'pass.docx', 'pass_funcionalidad.docx', 'pass.mp4');
