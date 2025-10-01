## API de Autenticación
- POST /api/register: Registra un usuario (name, email, password, password_confirmation).
- POST /api/login: Autentica un usuario y devuelve un token (email, password).
- Todas las rutas en /api/books están protegidas; usa el header Authorization: Bearer <token>.