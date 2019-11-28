# InkBox Challenge
This application is built as the technical challenge for InkBox. Goal is to generate print sheets containing all products under orders.

### Usage

This application is built under Laravel framework. Serve the application locally and the app can be accessed under `/public` folder

###Approach
1. Bound products for same order together

2. Shuffle the products under orders to get more combinations of insertion

3. For each order, try to insert every product under the order to the current  iterating sheet. If: 
	- can be inserted, process next order
	- cannot be inserted, process next order, if current order is end of order list, generate new sheet and reset iterating index

4. Store print sheets and sheet products accordingly, and record the x y positions. 

####Sheet Improvements
- 2x5 and 5x2 are essentially the same, can be flipped according to remaining space
e.g. row and count(matrix) === 2, then insert 2x5 first, same idea for column

- Shuffle orders

- Make shuffled arrays unique

- Instead of binding products to orders, products can be stored together and have an order_id attribute.

- Overflow causes 500 server reponse, create handler for it and resend request with fewer shuffle counts
