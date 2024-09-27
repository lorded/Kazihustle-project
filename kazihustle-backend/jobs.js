const mongoose = require('mongoose');

const JobSchema = new mongoose.Schema({
  category: { type: String, required: true },
  title: { type: String, required: true },
  description: { type: String, required: true },
  budget: { type: String, required: true },
  timeline: { type: String, required: true },
  first_name: { type: String, required: true },
  last_name: { type: String, required: true },
  email: { type: String, required: true },
  phone_number: { type: String, required: true },
  company_name: { type: String },
  company_size: { type: String }
});

module.exports = mongoose.model('Job', JobSchema);
