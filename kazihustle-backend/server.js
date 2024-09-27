const express = require('express');
const axios = require('axios');
const app = express();

// Configuration: Replace these values with your Google credentials
const clientID = '494067863288-45t3adnoqg05e223rpcbkbr6325j8l93.apps.googleusercontent.com';
const clientSecret = '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';
const redirectURI = 'http://127.0.0.1:5501/auth/login/google-oauth2/';

// Serve static files
app.use(express.static(__dirname));

// Initiate Google OAuth flow
app.get('/auth/google', (req, res) => {
    const state = 'randomUniqueString'; // Ideally generate a secure random string
    const googleAuthURL = `https://accounts.google.com/o/oauth2/auth?client_id=${clientID}&redirect_uri=${redirectURI}&response_type=code&scope=openid+email+profile&state=${state}`;
    res.redirect(googleAuthURL);
});

// OAuth callback route (handle the redirect after Google login)
app.get('/auth/login/google-oauth2/', async (req, res) => {
    const code = req.query.code; // Authorization code from Google
    const state = req.query.state; // Get the state from the query

    if (!code) {
        return res.send('Authorization code missing.');
    }

    try {
        // Exchange the authorization code for tokens
        const tokenResponse = await axios.post('https://oauth2.googleapis.com/token', {
            client_id: clientID,
            client_secret: clientSecret,
            redirect_uri: redirectURI,
            grant_type: 'authorization_code',
            code: code,
        });

        const accessToken = tokenResponse.data.access_token;
        const idToken = tokenResponse.data.id_token;

        // Store tokens in session or cookies as needed (skipping for simplicity)

        // Redirect to the dashboard after successful login
        res.redirect('/dashboard/index.html');
    } catch (error) {
        console.error('Error exchanging authorization code for tokens:', error);
        res.send('Login failed. Please try again.');
    }
});

// Start the server
app.listen(5501, () => {
    console.log('Server running on http://127.0.0.1:5501');
});
