const express = require('express');
const { OAuth2Client } = require('google-auth-library');
const router = express.Router();

const CLIENT_ID = '494067863288-45t3adnoqg05e223rpcbkbr6325j8l93.apps.googleusercontent.com';
const client = new OAuth2Client(CLIENT_ID);

router.get('/complete/google-oauth2/', async (req, res) => {
    const { code, state } = req.query; // Get code and state from query parameters

    try {
        const { tokens } = await client.getToken(code);
        client.setCredentials(tokens);
        
        // Redirect to dashboard after successful login
        res.redirect('/dashboard/index.html');
    } catch (error) {
        console.error('Error exchanging code for tokens:', error);
        res.status(500).send('Authentication failed');
    }
});

module.exports = router;
