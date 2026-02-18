INSERT INTO users (username, password_hash) VALUES
('admin', '$2y$12$c5LElCFrsOzaT7j4Yb0ExuQWOXhb0KXH.pPdZ5UK8jPwi4zqJVWrO');

-- IMPORTANTE: Cambia project_url y docx_url por las URLs reales en tu entorno.
INSERT INTO projects (name, project_url, docx_url) VALUES
('Portal de Distribuidores Prosa Natural', 'https://cambiar-por-tu-url-portal.example.com', 'https://cambiar-por-tu-docx-portal.example.com/documentacion.docx'),
('Prosa Natural Web', 'https://cambiar-por-tu-url-web.example.com', 'https://cambiar-por-tu-docx-web.example.com/documentacion.docx'),
('Pass', 'https://cambiar-por-tu-url-pass.example.com', 'https://cambiar-por-tu-docx-pass.example.com/documentacion.docx');
