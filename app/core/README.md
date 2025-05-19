# FoodieHub Helper Functions

This directory contains helper classes and functions for the FoodieHub application.

## Available Helper Classes

### Helpers

The `Helpers` class provides static utility methods for common tasks:

- `formatPrice($price, $currency = '$', $decimals = 2)` - Format a price with currency symbol
- `formatDate($date, $format = 'M d, Y')` - Format a date
- `formatDateTime($datetime, $format = 'M d, Y h:i A')` - Format a datetime
- `limitString($string, $limit, $append = '...')` - Limit a string to a certain number of characters
- `slugify($string)` - Convert a string to slug format
- `randomString($length = 10, $characters = '...')` - Generate a random string
- `startsWith($haystack, $needle)` - Check if a string starts with a specific substring
- `endsWith($haystack, $needle)` - Check if a string ends with a specific substring
- `currentUrl($withQueryString = true)` - Get the current URL
- `baseUrl()` - Get the base URL
- `isAjax()` - Check if the current request is AJAX
- `getIpAddress()` - Get the client's IP address
- `sanitize($input)` - Sanitize user input
- `generateCsrfToken()` - Generate a CSRF token
- `verifyCsrfToken($token)` - Verify CSRF token
- `redirect($url, $statusCode = 302)` - Redirect to a URL
- `getFileExtension($filename)` - Get file extension
- `isImage($filename)` - Check if file is an image
- `formatFileSize($bytes, $precision = 2)` - Format file size
- `timeAgo($datetime)` - Get time ago string

### FileUpload

The `FileUpload` class handles file uploads with validation and processing:

- `__construct($uploadDir = 'uploads', $allowedExtensions = [], $maxFileSize = 0)`
- `setAllowedExtensions($extensions)` - Set allowed file extensions
- `setMaxFileSize($size)` - Set maximum file size
- `setUploadDir($dir)` - Set upload directory
- `upload($file, $newFilename = '')` - Upload a file
- `uploadImage($file, $newFilename = '', $maxWidth = 0, $maxHeight = 0, $quality = 90)` - Upload an image with resizing
- `getUploadedFilePath()` - Get uploaded file path
- `getErrors()` - Get errors

### Validator

The `Validator` class validates form data with various rules:

- `__construct($data = [], $rules = [], $customMessages = [])`
- `setData($data)` - Set data to validate
- `setRules($rules)` - Set validation rules
- `setCustomMessages($messages)` - Set custom error messages
- `validate()` - Validate data against rules
- `getErrors()` - Get all errors
- `getFirstErrors()` - Get first error for each field
- `getAllErrors()` - Get all errors as a flat array
- `hasError($field)` - Check if field has errors
- `getFieldErrors($field)` - Get errors for a field

### Pagination

The `Pagination` class handles pagination for database queries:

- `__construct($totalItems, $itemsPerPage = 10, $currentPage = 1, $maxPageLinks = 5)`
- `getOffset()` - Get offset for SQL LIMIT clause
- `getLimit()` - Get limit for SQL LIMIT clause
- `getCurrentPage()` - Get current page
- `getTotalPages()` - Get total pages
- `getTotalItems()` - Get total items
- `getItemsPerPage()` - Get items per page
- `hasPreviousPage()` - Check if there is a previous page
- `hasNextPage()` - Check if there is a next page
- `getPreviousPage()` - Get previous page
- `getNextPage()` - Get next page
- `getPageRange()` - Get page range
- `getPaginationData()` - Get pagination data
- `render($baseUrl, $queryParam = 'page')` - Render pagination HTML

### Notification

The `Notification` class handles in-app notifications:

- `__construct()`
- `create($userId, $type, $message, $data = [], $link = '')` - Create a notification
- `getForUser($userId, $limit = 10, $offset = 0, $unreadOnly = false)` - Get notifications for a user
- `getById($id)` - Get notification by ID
- `markAsRead($id)` - Mark notification as read
- `markAllAsRead($userId)` - Mark all notifications as read for a user
- `delete($id)` - Delete notification
- `deleteAllForUser($userId)` - Delete all notifications for a user
- `countUnread($userId)` - Count unread notifications for a user
- `createOrderStatusNotification($orderId, $status)` - Create a notification for order status change
- `createNewOrderNotification($orderId)` - Create a notification for new order
- `createNewReviewNotification($reviewId)` - Create a notification for new review

### CartManager

The `CartManager` class manages shopping cart operations:

- `__construct($userId = null)`
- `setUserId($userId)` - Set user ID
- `addItem($productId, $quantity = 1)` - Add item to cart
- `updateQuantity($cartId, $quantity)` - Update item quantity
- `removeItem($cartId)` - Remove item from cart
- `clearCart()` - Clear cart
- `getItems()` - Get cart items
- `getItemsByVendor()` - Get cart items grouped by vendor
- `getTotal()` - Get cart total
- `getItemCount()` - Get cart item count
- `isEmpty()` - Check if cart is empty
- `hasMultipleVendors()` - Check if cart has items from multiple vendors
- `getVendorIds()` - Get vendor IDs in cart
- `getItemsForVendor($vendorId)` - Get cart items by vendor
- `getTotalForVendor($vendorId)` - Get cart total for vendor

## Global Helper Functions

The application also provides global helper functions in `app/helpers.php`:

- `format_price($price, $currency = '$', $decimals = 2)` - Format a price with currency symbol
- `format_date($date, $format = 'M d, Y')` - Format a date
- `format_datetime($datetime, $format = 'M d, Y h:i A')` - Format a datetime
- `limit_string($string, $limit, $append = '...')` - Limit a string to a certain number of characters
- `slugify($string)` - Convert a string to slug format
- `current_url($withQueryString = true)` - Get the current URL
- `base_url()` - Get the base URL
- `sanitize($input)` - Sanitize user input
- `csrf_token()` - Generate a CSRF token
- `csrf_field()` - Generate a CSRF token field
- `is_ajax()` - Check if the current request is AJAX
- `time_ago($datetime)` - Get time ago string
- `is_logged_in()` - Check if user is logged in
- `current_user()` - Get the authenticated user
- `has_role($role)` - Check if the authenticated user has a specific role
- `flash($key, $default = null)` - Get a flash message
- `has_flash($key)` - Check if a flash message exists
- `session($key, $default = null)` - Get a session variable
- `has_session($key)` - Check if a session variable exists
- `request_method()` - Get the current request method
- `is_post()` - Check if the current request method is POST
- `is_get()` - Check if the current request method is GET
- `request($key, $default = null)` - Get a request variable
- `has_request($key)` - Check if a request variable exists
- `redirect($url, $statusCode = 302)` - Redirect to a URL
- `get_ip_address()` - Get the client's IP address
- `format_file_size($bytes, $precision = 2)` - Format file size
- `url_is($pattern)` - Check if the current URL matches a pattern
- `active_class($pattern, $class = 'active')` - Get the active class if the current URL matches a pattern
- `asset($path)` - Get the asset URL
- `public_path($path = '')` - Get the public path
- `storage_path($path = '')` - Get the storage path
- `app_path($path = '')` - Get the app path
- `root_path($path = '')` - Get the root path
- `random_string($length = 10, $characters = '...')` - Generate a random string
- `starts_with($haystack, $needle)` - Check if a string starts with a specific substring
- `ends_with($haystack, $needle)` - Check if a string ends with a specific substring
