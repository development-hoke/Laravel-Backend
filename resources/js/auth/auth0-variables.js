export const AUTH_CONFIG = {
  clientId: 'EU403vWtwHgHgjqQUrvo6EVzAaqAWkg9',
  domain: 'dev-zmpm22ar.auth0.com',
  callbackUrl: process.env.NODE_ENV === 'development' ? 'http://127.0.0.1:8000/callback' : 'https://vuely.theironnetwork.org/callback',
  apiUrl: 'API_IDENTIFIER'
}