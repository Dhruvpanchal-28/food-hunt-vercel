const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());

// Database connection
async function getConnection() {
  return await mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || '',
    database: process.env.DB_NAME || 'dbfood'
  });
}

// Routes
app.get('/api/foods', async (req, res) => {
  try {
    const connection = await getConnection();
    const [rows] = await connection.execute(`
      SELECT f.*, v.fld_name as vendor_name 
      FROM tbfood f 
      JOIN tblvendor v ON f.fldvendor_id = v.fldvendor_id 
      ORDER BY f.foodname
    `);
    await connection.end();
    res.json(rows);
  } catch (error) {
    console.error('Error fetching foods:', error);
    res.status(500).json({ error: 'Database error' });
  }
});

app.get('/api/vendors', async (req, res) => {
  try {
    const connection = await getConnection();
    const [rows] = await connection.execute('SELECT * FROM tblvendor ORDER BY fld_name');
    await connection.end();
    res.json(rows);
  } catch (error) {
    console.error('Error fetching vendors:', error);
    res.status(500).json({ error: 'Database error' });
  }
});

app.post('/api/login', async (req, res) => {
  try {
    const { email, password } = req.body;
    const connection = await getConnection();
    
    // Check customer login
    const [customers] = await connection.execute(
      'SELECT * FROM tblcustomer WHERE fld_email = ? AND fld_password = ?',
      [email, password]
    );
    
    if (customers.length > 0) {
      await connection.end();
      return res.json({ success: true, user: customers[0], type: 'customer' });
    }
    
    // Check vendor login
    const [vendors] = await connection.execute(
      'SELECT * FROM tblvendor WHERE fld_email = ? AND fld_password = ?',
      [email, password]
    );
    
    if (vendors.length > 0) {
      await connection.end();
      return res.json({ success: true, user: vendors[0], type: 'vendor' });
    }
    
    await connection.end();
    res.status(401).json({ error: 'Invalid credentials' });
  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({ error: 'Login failed' });
  }
});

app.post('/api/register', async (req, res) => {
  try {
    const { name, email, phone, address, password } = req.body;
    const connection = await getConnection();
    
    const [result] = await connection.execute(
      'INSERT INTO tblcustomer (fld_name, fld_email, fld_password, fld_mob, fld_address) VALUES (?, ?, ?, ?, ?)',
      [name, email, password, phone, address]
    );
    
    await connection.end();
    res.json({ success: true, customer_id: result.insertId });
  } catch (error) {
    console.error('Registration error:', error);
    res.status(500).json({ error: 'Registration failed' });
  }
});

app.get('/api/search', async (req, res) => {
  try {
    const { q, type = 'food' } = req.query;
    if (!q) {
      return res.status(400).json({ error: 'Search term required' });
    }
    
    const connection = await getConnection();
    let query, params;
    
    if (type === 'food') {
      query = `
        SELECT f.*, v.fld_name as vendor_name 
        FROM tbfood f 
        JOIN tblvendor v ON f.fldvendor_id = v.fldvendor_id 
        WHERE f.foodname LIKE ? OR f.cuisines LIKE ?
      `;
      params = [`%${q}%`, `%${q}%`];
    } else {
      query = 'SELECT * FROM tblvendor WHERE fld_name LIKE ? OR fld_address LIKE ?';
      params = [`%${q}%`, `%${q}%`];
    }
    
    const [rows] = await connection.execute(query, params);
    await connection.end();
    res.json(rows);
  } catch (error) {
    console.error('Search error:', error);
    res.status(500).json({ error: 'Search failed' });
  }
});

app.post('/api/orders', async (req, res) => {
  try {
    const { customer_id, food_id, quantity = 1, total_price = 0 } = req.body;
    const connection = await getConnection();
    
    const [result] = await connection.execute(
      'INSERT INTO tblorders (customer_id, food_id, quantity, total_price, status) VALUES (?, ?, ?, ?, "pending")',
      [customer_id, food_id, quantity, total_price]
    );
    
    await connection.end();
    res.json({ success: true, order_id: result.insertId });
  } catch (error) {
    console.error('Order error:', error);
    res.status(500).json({ error: 'Order failed' });
  }
});

// Health check
app.get('/health', (req, res) => {
  res.json({ status: 'OK', timestamp: new Date().toISOString() });
});

// Start server
app.listen(PORT, () => {
  console.log(`Food Hunt API running on port ${PORT}`);
});
