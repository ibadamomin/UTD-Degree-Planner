const express = require("express");
const mysql = require("mysql");
const cors = require("cors");
const app = express();
app.use(cors());
fs = require("fs");
require("dotenv").config();

var filePath = "./certificates/DigiCertGlobalRootCA.crt.pem";
var database_password = process.env.SERVER_PASSWORD;

var conn = mysql.createConnection({
  host: "degreeplanner.mysql.database.azure.com",
  user: "CS4347",
  password: database_password,
  database: "degreeplanner",
  port: 3306,
  ssl: {
    ca: fs.readFileSync(filePath),
  },
});

conn.connect(function (err) {
  if (err) console.log("Error: " + err);
  else console.log("Connected!");
});

app.get("/", (req, res) => {
  res.send("Welcome to UTD Degree Planner!");
});

app.get("/users", (req, res) => {
  conn.query("SELECT * FROM users", function (err, result) {
    if (err) throw err;
    res.send(result);
  });
});

app.listen(8000, () => {
  console.log(`Server is running http://localhost:8000`);
});
