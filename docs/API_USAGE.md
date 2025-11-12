# API Usage — Auth endpoints

Base URL (local dev): `http://localhost`

Endpoints:
- `POST /api/register` — Register new user. Returns JSON { user, token }
- `POST /api/login` — Log in user. Returns JSON { user, token }
- `GET /api/user` — Get authenticated user (requires Bearer token)
- `POST /api/logout` — Revoke current token (requires Bearer token)
- `GET /api/users` — Get all users (requires Bearer token)

## curl examples

### Register

```bash
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Budi","email":"budi@example.test","password":"secret123"}'
```

Successful response (201):

```json
{
  "user": {
    "id": 123,
    "name": "Budi",
    "email": "budi@example.test",
    ...
  },
  "token": "1|k7..."
}
```

### Login

```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"budi@example.test","password":"secret123"}'
```

Successful response (200):

```json
{
  "user": { ... },
  "token": "1|k7..."
}
```

### Using the token

Assign the token to a shell variable and call protected endpoints:

```bash
TOKEN="$(curl -s -X POST http://localhost/api/login -H "Content-Type: application/json" -d '{"email":"budi@example.test","password":"secret123"}' | jq -r '.token')"

# get current user
curl -H "Authorization: Bearer $TOKEN" http://localhost/api/user

# logout
curl -X POST -H "Authorization: Bearer $TOKEN" http://localhost/api/logout
```

If you don't have `jq`, you can copy the token value from the login response and use it directly:

```bash
curl -H "Authorization: Bearer 1|k7..." http://localhost/api/user
```

### List all users (admin)

```bash
curl -H "Authorization: Bearer $TOKEN" http://localhost/api/users
```

Response:
```json
{
  "users": [
    { "id": 1, "name": "Budi", ... },
    { "id": 2, "name": "Siti", ... },
    ...
  ]
}
```

> Note: This endpoint is open to any authenticated user. Tambahkan pengecekan admin jika ingin membatasi akses.

## Notes
- The examples assume the app runs on `http://localhost`. If your `APP_URL` differs, replace the base URL accordingly.
- The login/register endpoints return a plaintext personal access token issued by Sanctum. Keep it secret.
- For browser-based SPA auth, use the `sanctum/csrf-cookie` endpoint and session cookies instead of tokens.

## Postman import
- See `docs/postman_collection.json` in this repo. Import it into Postman to run ready-made requests.

## Upload profile photo (multipart/form-data)

This project includes an API endpoint to update the authenticated user's profile (including a profile photo) at:

- `PATCH /api/profile` — requires `Authorization: Bearer {token}` and accepts multipart/form-data field `profile_photo` (also accepts `name` and `email`).

Example curl (use the token returned by `/api/login`):

```bash
curl -X PATCH http://localhost/api/profile \
  -H "Authorization: Bearer ${TOKEN}" \
  -F "profile_photo=@/path/to/photo.jpg" \
  -F "name=Budi Baru" \
  -F "email=budi.new@example.test"
```

Notes:
- Use `-F` to send multipart form fields. The `profile_photo` field should be a file path prefixed with `@`.
- The API will store the uploaded file under `storage/app/public/profile-photos` and return the updated `user` object in JSON.

Postman: a `Upload Profile Photo` request has been added to `docs/postman_collection.json` — set `bearer_token` variable with the token and use the Body type `form-data` with a file key `profile_photo`.
