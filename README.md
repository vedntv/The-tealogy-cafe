Feel free to contact us! 💖 <br>
View the Web App here: 

# The Tealogy Cafe Management 
The "The Tealogy Cafe Management" project is an open-source web application designed to streamline the operations of a café or a coffee shop. An online portal for cafe for ordering coffee or snacks. It is developed using HTML, CSS, and JavaScript, making it easily accessible and modifiable for contributors. The project is hosted on GitHub. <br>


## Features
🔰 **Menu Display:** The application showcases the cafe's menu items with detailed descriptions, prices, and images for each item such as coffees, pastries, sandwiches, and other offerings.

🌟 **Ordering System:** Users can select and add multiple items to their cart, enabling them to place and modify their orders as needed. The system allows customization options for various items (e.g., choosing coffee size, milk preference, etc.).

🔥 **Contact Information:** Users can directly contact the shop via the Contact Us form, allowing a seamless connection.

🌠 **FAQs:** There are pre-answered questions mentioned in the FAQ section to provide relevant information to the users. Set of predefined questions have been used.
## Testing Recommendations

1. **Database Setup:**
   bash
   # Run master SQL file
   mysql -u root < Database/tealogy_login.sql
   # OR run migration steps if existing database
   ```

2. **Admin Setup:**
   - Open `create_root.php` in browser to create root user
   - Default: email=root@tealogy.local, password=admin123
   - Delete create_root.php after use

3. **Functional Tests:**
   - Add items to cart, verify localStorage persistence
   - Sign up with extended profile, verify email/username uniqueness
   - Login with different roles, verify access control
   - Access admin_users.php as root, change user roles
   - Place order, verify cart and confirmation

4. **Visual Verification:**
   - Check navbar consistency across all pages
   - Verify currency symbols (₹) on menu, cart, and order pages
   - Test responsive design on mobile
   - Verify role badges display on homepage

## Tech Stack

- HTML
- CSS
- Bootstrap
- Javascript
- JQuery

## Authors
  VED PRAKASH PANDEY <br>
  vedntv@gmail.com
- [@vedntv](https://www.github.com/vedntv)

## Maintained By
- Ved Prakash Pandey, gautambuddha nagar ward-14, nautanwa nagar panchayat, maharajganj, UP
