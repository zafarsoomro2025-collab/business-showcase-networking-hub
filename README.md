# Business Showcase & Networking Hub

A comprehensive WordPress plugin for showcasing businesses with advanced search, filtering, star ratings, reviews, and networking functionality.

## Description

Business Showcase & Networking Hub is a feature-rich WordPress plugin designed to help you create a professional business directory with powerful search capabilities, live filtering, and user engagement features.

### 🎯 Key Features

#### 🔍 AJAX Live Search (NEW!)
- **Real-time search** as you type (minimum 2 characters)
- **Smart search** across business names, categories, services, and ratings
- **Live results dropdown** with thumbnails, ratings, and excerpts
- **Debounced input** for optimal performance
- **Mobile-optimized** responsive interface
- **Combined filtering** with categories and services
- **No page reloads** - smooth, instant results

#### 📋 Business Directory
- Custom post type for Business Profiles
- Custom taxonomy for Business Categories
- Comprehensive business information storage
- Featured business highlighting
- Flexible display options

#### ⭐ Rating & Review System
- 5-star rating system
- Detailed review comments
- Average rating calculation
- Review count display
- Ratings visible in search results

#### 🎨 Display Options
- **Shortcodes**: `[business_directory]`, `[featured_businesses]`
- **Gutenberg Blocks**: Business Directory, Success Stories
- Multiple layout options (grid, list, cards)
- Responsive design for all devices

#### 🎛️ Advanced Filtering
- Filter by business category
- Filter by services offered
- Search by keyword
- AJAX-powered (no page reload)
- Combined filter support

#### 📧 Contact Forms
- Business-specific contact forms
- AJAX form submission
- Email notifications
- Spam protection with nonces

#### 🛠️ Admin Features
- Custom admin columns (Featured, Rating, Category)
- Bulk actions (Mark/Remove Featured)
- CSV export functionality
- Detailed meta boxes for business data

#### 🔒 Security
- All inputs sanitized
- All outputs escaped
- Nonce verification on all forms
- CSRF protection
- SQL injection prevention
- XSS attack prevention

## Installation

1. Upload the plugin files to `/wp-content/plugins/business-showcase-networking-hub`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure settings under Business Profiles menu
4. Add your first business profile!

## Usage

### Adding a Business Profile

1. Go to **Business Profiles > Add New**
2. Fill in business details:
   - Business name and description
   - Contact information (email, website)
   - Social media links
   - Services offered
   - Business category
3. Upload business logo (featured image)
4. Check "Featured Business" if applicable
5. Publish the profile

### Using the Search Feature

#### For Site Visitors:

1. Navigate to any page with `[business_directory]` shortcode
2. See the search bar at the top
3. Start typing to search (minimum 2 characters)
4. View live results in dropdown:
   - Click any result to visit business profile
   - OR click "View all results" to see filtered grid
5. Combine with category/service filters for precise results
6. Click "Clear Search" to reset

#### Shortcode with Search:
```
[business_directory posts_per_page="12"]
```

The search interface is automatically included!

### Display Businesses

#### Shortcode - Full Directory:
```php
[business_directory posts_per_page="12" category="" featured_only="false"]
```

#### Shortcode - Featured Only:
```php
[featured_businesses limit="6"]
```

#### Gutenberg Block:
1. Add new block in editor
2. Search for "Business Showcase"
3. Select "Business Directory" or "Success Stories"
4. Configure block settings in sidebar

### Filtering Options

Users can filter businesses by:
- **Search**: Type keywords to search across all fields
- **Category**: Select from business categories
- **Service**: Filter by services offered
- **Combined**: Use search + filters together

### Enabling Reviews

Reviews are enabled by default on all Business Profile posts. Users can:
1. Visit any business profile page
2. Scroll to "Customer Reviews" section
3. Select star rating (1-5 stars)
4. Write review comment
5. Submit review

## Search Feature Details

### What Gets Searched?

The live search looks for matches in:
- ✅ Business title/name
- ✅ Business description/content
- ✅ Business categories
- ✅ Services offered
- ✅ Related metadata

### Search Results Include:

Each search result displays:
- Business logo/thumbnail
- Business name (clickable)
- Category tags
- Short excerpt
- Star rating and review count
- Featured badge (if applicable)

### Search Performance:

- **Debounce**: 300ms delay prevents excessive requests
- **Result Limit**: 10 live results in dropdown
- **Optimized**: Uses WordPress WP_Query efficiently
- **Cached**: Compatible with object caching

See [SEARCH-FEATURE.md](SEARCH-FEATURE.md) for complete documentation.

## Customization

### Customize Search Behavior

Change minimum characters:
```javascript
// In assets/js/script.js
if (query.length < 2) {  // Change to 3 or more
```

Change result limit:
```php
// In PHP AJAX handler
'posts_per_page' => 10,  // Increase for more results
```

### Styling the Search Interface

Override these CSS classes:
- `.business-search-input` - Search input field
- `.search-results-dropdown` - Results dropdown
- `.search-result-item` - Individual result item
- `.search-info` - Active search info bar

Example:
```css
.business-search-input {
    border-radius: 20px;
    padding: 18px 50px;
}

.search-result-item:hover {
    background: #f0f7ff;
}
```

## API / Hooks

### AJAX Endpoints

#### Live Search:
```javascript
action: 'business_showcase_live_search'
```

#### Filter Businesses:
```javascript
action: 'business_showcase_filter'
```

### PHP Functions

Get rating summary:
```php
$summary = business_showcase_get_rating_summary( $post_id );
echo $summary['html'];
```

Display star rating:
```php
echo business_showcase_display_star_rating( 4.5 );
```

Get business grid:
```php
echo business_showcase_get_business_grid( array(
    'posts_per_page' => 12,
    'category' => 'restaurants',
    'search' => 'pizza',
) );
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- jQuery (included with WordPress)
- Modern web browser with JavaScript enabled

## Browser Support

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Documentation

- [SEARCH-FEATURE.md](SEARCH-FEATURE.md) - Complete search documentation
- [SEARCH-QUICK-REFERENCE.md](SEARCH-QUICK-REFERENCE.md) - Developer quick reference
- [SECURITY.md](SECURITY.md) - Security implementation details

## Troubleshooting

### Search not working?

1. Check browser console for errors
2. Verify JavaScript is enabled
3. Confirm AJAX URL is correct
4. Clear browser cache
5. Check for plugin conflicts

### Dropdown not appearing?

1. Verify CSS is loaded
2. Check z-index conflicts
3. Inspect element in browser dev tools
4. Try different browser

### Slow search?

1. Enable object caching (Redis/Memcached)
2. Check database indexes
3. Reduce number of published businesses for testing
4. Check server resources

See [SEARCH-FEATURE.md](SEARCH-FEATURE.md) for detailed troubleshooting.

## Support

For issues, questions, or feature requests:
1. Check documentation files
2. Review code comments
3. Submit issue on GitHub repository

## Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Credits

Developed by Zafar Soomro
GitHub: https://github.com/zafarsoomro2025-collab

## Changelog

### 1.0.0 (December 12, 2025)
* ✅ Initial release
* ✅ Custom post type for Business Profiles
* ✅ Custom taxonomy for Business Categories
* ✅ Business meta boxes (contact, social, services)
* ✅ Featured business functionality
* ✅ Business directory shortcode with filtering
* ✅ Featured businesses shortcode
* ✅ Single business profile template
* ✅ Star rating system (5-star)
* ✅ Customer reviews section
* ✅ Admin custom columns (Featured, Rating, Category)
* ✅ CSV export functionality
* ✅ Bulk actions (Mark/Remove Featured)
* ✅ Gutenberg blocks (Business Directory, Success Stories)
* ✅ **AJAX Live Search** (NEW!)
  - Real-time search as you type
  - Live results dropdown with thumbnails
  - Search by name, category, services, rating
  - Mobile-responsive interface
  - Debounced input for performance
* ✅ Contact form with AJAX submission
* ✅ Conditional asset loading
* ✅ Comprehensive responsive CSS
* ✅ Complete security implementation
* ✅ Security documentation (SECURITY.md)

## Screenshots

1. **Search Interface** - Live search bar with auto-complete dropdown
2. **Search Results** - Real-time results with thumbnails and ratings
3. **Business Directory** - Grid layout with filters
4. **Business Profile** - Detailed business page with reviews
5. **Admin Panel** - Custom columns and bulk actions
6. **Gutenberg Blocks** - Block editor integration

## Roadmap

### Upcoming Features
- [ ] Search history and suggestions
- [ ] Advanced search filters (location, price range)
- [ ] Search analytics dashboard
- [ ] Fuzzy search matching
- [ ] Voice search support
- [ ] Save search functionality
- [ ] Email alerts for new businesses
- [ ] Business comparison feature
- [ ] Map integration
- [ ] Multi-language support

---

**Made with ❤️ for WordPress**
