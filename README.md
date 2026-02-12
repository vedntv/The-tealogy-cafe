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
   - Automatic server-side scripts were removed for security. See `create_root.html` for guidance on how to recreate an admin safely.
   - Default admin credentials (when recreated via SQL): email=root@tealogy.local, password=admin123
   - After creating an admin account, remove any temporary creation scripts and secure your database

3. **Functional Tests:**
   - Add items to cart, verify localStorage persistence
   - Sign up via the UI (note: server-side account creation is disabled in this copy)
   - Login via the UI (note: server-side authentication is disabled in this copy)
   - Admin features are placeholders; restore server-side components to test role management
   - Place order via the UI (note: checkout processing is disabled in this copy)

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
