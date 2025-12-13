# Search Feature - Quick Reference

## For Developers

### AJAX Endpoint
```
Action: business_showcase_live_search
URL: admin-ajax.php
Method: POST
```

### Request Parameters
```php
$_POST['action'] = 'business_showcase_live_search';
$_POST['nonce'] = 'business_showcase_nonce';
$_POST['search'] = 'search query';
```

### Response Format
```json
{
  "success": true,
  "data": {
    "results": [
      {
        "id": 123,
        "title": "Business Name",
        "url": "https://example.com/business/name",
        "excerpt": "Short description...",
        "thumbnail": "https://example.com/image.jpg",
        "categories": ["Category 1", "Category 2"],
        "services": ["Service 1", "Service 2"],
        "rating": 4.5,
        "rating_count": 12,
        "is_featured": true
      }
    ],
    "count": 15,
    "query": "search query"
  }
}
```

## Key Files Modified

### PHP
- `business-showcase-networking-hub.php`
  - Lines ~655-695: Search bar HTML
  - Lines ~835-850: Added search parameter to grid function
  - Lines ~990-1010: Updated filter handler
  - Lines ~1010-1130: New live search handler

### JavaScript
- `assets/js/script.js`
  - Lines ~38-200: Search functionality
  - Updated `filterBusinesses()` function

### CSS
- `assets/css/style.css`
  - Lines ~65-265: Search interface styles
  - Lines ~1270-1330: Tablet responsive
  - Lines ~1626-1700: Mobile responsive

## Testing Checklist

- [ ] Search with 1 character (should show message)
- [ ] Search with 2+ characters (should show results)
- [ ] Click result item (should navigate)
- [ ] Click "View all results" (should filter grid)
- [ ] Click clear button (should reset)
- [ ] Test on mobile (responsive layout)
- [ ] Test with no results (should show message)
- [ ] Test with special characters
- [ ] Test combined with category filter
- [ ] Test combined with service filter

## Common Customizations

### Change Minimum Characters
```javascript
// In script.js, line ~54
if (query.length < 2) {  // Change 2 to your value
```

### Change Debounce Delay
```javascript
// In script.js, line ~65
searchTimeout = setTimeout(function() {
    performLiveSearch(query);
}, 300);  // Change 300ms to your value
```

### Change Result Limit
```php
// In business-showcase-networking-hub.php, line ~1025
'posts_per_page' => 10,  // Change 10 to your value
```

### Customize Search Fields
```php
// In business_showcase_live_search() function
// Add custom meta queries or tax queries
$meta_query[] = array(
    'key' => '_your_custom_field',
    'value' => $search_query,
    'compare' => 'LIKE',
);
```

## Hooks & Filters (Future)

### Planned Filter Hooks
```php
// Modify search query args
apply_filters('business_showcase_search_args', $query_args, $search_query);

// Modify search results
apply_filters('business_showcase_search_results', $results, $search_query);

// Customize result display
apply_filters('business_showcase_search_result_item', $result, $post_id);
```

### Planned Action Hooks
```php
// Before search execution
do_action('business_showcase_before_search', $search_query);

// After search execution
do_action('business_showcase_after_search', $results, $search_query);
```

## Performance Tips

1. **Enable Object Caching**: Use Redis or Memcached
2. **Optimize Database**: Ensure indexes on wp_posts table
3. **Use CDN**: Serve CSS/JS from CDN for faster load
4. **Lazy Load Images**: Implement lazy loading for thumbnails
5. **Minify Assets**: Minify CSS and JS for production

## Security Notes

✅ **Implemented**:
- Nonce verification on AJAX requests
- Input sanitization with `sanitize_text_field()`
- Output escaping in dropdown HTML
- Rate limiting via debounce (client-side)

⚠️ **Consider Adding**:
- Server-side rate limiting (WordPress Transients)
- IP-based throttling for anonymous users
- Search query logging for abuse detection
- CAPTCHA for excessive searches

## Browser Console Testing

```javascript
// Test AJAX call directly
jQuery.ajax({
    url: businessShowcaseAjax.ajaxurl,
    type: 'POST',
    data: {
        action: 'business_showcase_live_search',
        nonce: businessShowcaseAjax.nonce,
        search: 'test'
    },
    success: function(response) {
        console.log('Search results:', response);
    }
});

// Check if search is initialized
console.log('Current search:', window.businessCurrentSearch);

// Trigger search programmatically
jQuery('#business-search').val('restaurant').trigger('input');
```
