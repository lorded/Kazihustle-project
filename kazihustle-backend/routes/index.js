const express = require('express');
const path = require('path');
const router = express.Router();

// Home route
router.get('/', (req, res) => {
  res.send('Welcome to Kazihustle!');
});

// About route
router.get('/about', (req, res) => {
  res.send('This is the About page.');
});

// Contact route
router.get('/contact', (req, res) => {
  res.send('Contact us at: contact@kazihustle.com');
});

// Jobs create route - serve the existing HTML file
router.get('/jobs/create', (req, res) => {
  // Serve the index.html file located in the specified directory
  res.sendFile(path.join(__dirname, '../../jobs/create/index.html')); // Adjust path as necessary
});

// Export the router
module.exports = router;