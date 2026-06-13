import mysql.connector
from werkzeug.security import check_password_hash
import re

def authenticate_student(student_id, password):
    """Authenticate a student using their ID and password"""
    try:
        # Connect to the database
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="kyambogo_voting_system"
        )
        
        cursor = conn.cursor(dictionary=True)
        
        # Check if student exists
        query = "SELECT * FROM students WHERE student_id = %s"
        cursor.execute(query, (student_id,))
        student = cursor.fetchone()
        
        if not student:
            return None, "Invalid student ID"
        
        # Verify password
        if not check_password_hash(student['password_hash'], password):
            return None, "Incorrect password"
        
        # Check if student has already voted
        if student['has_voted']:
            return None, "You have already voted"
        
        # Return student data without password hash
        student_data = {
            'student_id': student['student_id'],
            'first_name': student['first_name'],
            'last_name': student['last_name'],
            'email': student['email'],
            'faculty': student['faculty'],
            'department': student['department']
        }
        
        return student_data, None
        
    except mysql.connector.Error as err:
        return None, f"Database error: {err}"
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

def validate_email(email):
    """Validate email format"""
    pattern = r"^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
    return re.match(pattern, email) is not None