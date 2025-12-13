# AJAX Search & Filtering Feature

## Overview

The Business Showcase & Networking Hub plugin now includes a powerful AJAX-based search system that allows users to search for businesses in real-time without page reloads.

## Features

### 🔍 Live Search
- **Real-time Results**: Search results appear as you type (minimum 2 characters)
- **Debounced Input**: Search requests are optimized with 300ms debounce
- **Dropdown Interface**: Results displayed in an elegant dropdown below the search bar
- **Auto-complete**: Click any result to navigate directly to the business profile

### 🎯 Search Capabilities
The search feature searches across:
1. **Business Name** - Primary title search
2. **Business Category** - Taxonomy term matching
3. **Business Services** - Meta field matching
4. **Business Content** - Description/excerpt search
5. **Rating Information** - Filter by rating scores

### 📊 Search Results Display
Each result shows:
- Business thumbnail/logo (with placeholder if missing)
- Business title (clickable link)
- Business categories
- Excerpt (truncated to 15 words)
- Star rating with review count
- Featured badge (if applicable)

### 🔄 Filter Integration
- Search works alongside existing category and service filters
- Combined filtering: Search + Category + Service
- Clear search button to reset search criteria
- Search info bar showing active search query and result count

## User Interface

### Search Bar
```
┌─────────────────────────────────────────────────────────┐
│ 🔍 Search businesses by name, category, services...  ✕ │
└─────────────────────────────────────────────────────────┘
```

### Live Results Dropdown
```
┌─────────────────────────────────────────────────────────┐
│ 15 results found                  [View all results]    │
├─────────────────────────────────────────────────────────┤
│ [IMG] Business Name                         [Featured]  │
│       Category Name                                      │
│       Short description of the business...               │
│       ★★★★☆ 4.5 (12 reviews)                           │
├─────────────────────────────────────────────────────────┤
│ [IMG] Another Business                                   │
│       ...                                                │
└─────────────────────────────────────────────────────────┘
```

### Search Info Bar (After "View All Results")
```
┌─────────────────────────────────────────────────────────┐
│ Search: "restaurant" - 8 results    [Clear Search]     │
└─────────────────────────────────────────────────────────┘
```

## Technical Implementation

### PHP Functions

#### `business_showcase_live_search()`
**Location**: `business-showcase-networking-hub.php` (line ~1000-1130)

**Purpose**: AJAX handler for live search requests

**Parameters** (via $_POST):
- `action`: 'business_showcase_live_search'
- `nonce`: Security nonce
- `search`: Search query string

**Returns** (JSON):
```php
array(
    'results' => array(
        array(
            'id' => int,
            'title' => string,
            'url' => string,
            'excerpt' => string,
            'thumbnail' => string|false,
            'categories' => array,
            'services' => array,
            'rating' => float,
            'rating_count' => int,
            'is_featured' => bool,
        ),
        // ... more results
    ),
    'count' => int,
    'query' => string,
)
```

**Search Logic**:
1. Validates nonce and search query (min 2 characters)
2. Builds WP_Query with search parameter
3. Searches in post title and content
4. Includes taxonomy search for categories
5. Returns up to 10 results ordered by relevance

#### Updated: `business_showcase_filter_businesses()`
**Enhancement**: Now accepts `search` parameter alongside `category` and `service`

#### Updated: `business_showcase_get_business_grid()`
**Enhancement**: Accepts `search` parameter in args array

### JavaScript Functions

#### `initLiveSearch()`
**Location**: `assets/js/script.js` (line ~38-87)

**Purpose**: Initialize live search functionality

**Features**:
- Input event handling with debounce
- Clear button toggle
- Dropdown show/hide logic
- Click outside to close
- Search filter button handling

#### `performLiveSearch(query)`
**Location**: `assets/js/script.js` (line ~89-113)

**Purpose**: Execute AJAX search request

**Parameters**:
- `query` (string): Search term

**AJAX Call**:
```javascript
$.ajax({
    url: businessShowcaseAjax.ajaxurl,
    type: 'POST',
    data: {
        action: 'business_showcase_live_search',
        nonce: businessShowcaseAjax.nonce,
        search: query
    }
});
```

#### `displaySearchResults(data)`
**Location**: `assets/js/script.js` (line ~115-180)

**Purpose**: Render search results in dropdown

**Features**:
- Result count display
- "View all results" button
- Result item rendering with thumbnails
- Star rating generation
- Featured badge display

#### `generateStars(rating)`
**Location**: `assets/js/script.js` (line ~182-200)

**Purpose**: Generate star rating HTML

**Parameters**:
- `rating` (float): Rating value 0-5

**Returns**: String with full stars (★), half stars (½), and empty stars (☆)

#### Updated: `filterBusinesses()`
**Enhancement**: Now includes search parameter from `window.businessCurrentSearch`

## CSS Styling

### Main Classes

#### `.business-search-container`
- Container for entire search interface
- Relative positioning for dropdown
- Margin bottom for spacing

#### `.business-search-input`
- Full-width input field
- Padding with space for icons
- Focus states with blue border
- Box shadow on focus

#### `.search-results-dropdown`
- Absolute positioned dropdown
- Max height with scroll
- Border and shadow
- Slide-down animation

#### `.search-result-item`
- Flex layout for thumbnail + content
- Hover background effect
- Border between items
- Clickable link styling

#### `.search-info`
- Blue background info bar
- Flex layout with gap
- Shows active search query
- Contains clear button

### Responsive Breakpoints

**768px and below**:
- Smaller font sizes
- Reduced padding
- Full-width buttons
- Stacked search info layout

**480px and below**:
- Extra compact design
- Smaller thumbnails (60x60)
- Reduced dropdown height
- Tighter spacing

## Usage Examples

### User Workflow 1: Quick Search
1. User types "coffee" in search bar
2. Live results appear in dropdown after 300ms
3. User clicks on "Coffee House" result
4. Navigates to business profile page

### User Workflow 2: View All Results
1. User types "restaurant"
2. Dropdown shows "15 results found"
3. User clicks "View all results" button
4. Grid updates to show filtered results
5. Search info bar appears with "restaurant" - 15 results
6. User can apply additional filters (category, service)

### User Workflow 3: Combined Filtering
1. User searches for "bakery"
2. Clicks "View all results"
3. Selects "Food & Beverage" category filter
4. Selects "Delivery" service filter
5. Grid shows businesses matching all criteria

### User Workflow 4: Clear Search
1. User has active search filter
2. Clicks "Clear Search" in info bar (or X in search input)
3. Search is cleared
4. Grid returns to showing all businesses

## Security

### Input Sanitization
- Search query: `sanitize_text_field()`
- All user inputs validated before processing

### Output Escaping
- All HTML output properly escaped
- JSON responses use `wp_send_json_success()`
- Image URLs: `esc_url()`
- Text content: `esc_html()`

### Nonce Verification
- All AJAX requests verify nonce: `check_ajax_referer()`
- Nonce action: `business_showcase_nonce`
- Works for both logged-in and logged-out users

### Query Security
- Uses WP_Query (no direct SQL)
- All query args sanitized
- Meta queries use proper comparison operators

## Performance Optimization

### Debouncing
- 300ms debounce on search input
- Prevents excessive AJAX requests
- Improves server performance

### Result Limiting
- Live search limited to 10 results
- Prevents large payload transfers
- Quick response times

### Caching
- WordPress object caching compatible
- Transient-ready architecture
- No custom caching required

### Database Optimization
- Indexed searches (WordPress core)
- Efficient WP_Query usage
- Minimal meta queries

## Browser Compatibility

✅ **Modern Browsers**:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

✅ **Mobile Browsers**:
- iOS Safari 14+
- Chrome Mobile 90+
- Samsung Internet 14+

⚠️ **Legacy Support**:
- IE11: Not tested (plugin requires modern JavaScript)
- Older browsers may need polyfills

## Accessibility

### Keyboard Navigation
- Tab through search input
- Arrow keys work in input field
- Enter to navigate (if on result link)
- Escape to close dropdown (via click outside)

### Screen Readers
- Placeholder text provides context
- Search results have semantic HTML
- ARIA labels on buttons
- Loading states announced

### Visual Accessibility
- High contrast text
- Focus indicators on inputs
- Clear button states
- Readable font sizes (14-16px)

## Troubleshooting

### Search Not Working
**Issue**: No results appear when typing

**Solutions**:
1. Check browser console for JavaScript errors
2. Verify AJAX URL is correct: `businessShowcaseAjax.ajaxurl`
3. Confirm nonce is present: `businessShowcaseAjax.nonce`
4. Check WordPress REST API is enabled
5. Verify at least 2 characters entered

### Dropdown Not Appearing
**Issue**: Search results dropdown doesn't show

**Solutions**:
1. Check CSS is loaded: `style.css`
2. Verify no CSS conflicts with theme
3. Check z-index issues with other elements
4. Inspect element to confirm HTML is generated

### Slow Search Performance
**Issue**: Search takes too long

**Solutions**:
1. Check server response time in Network tab
2. Verify database indexes exist on post table
3. Reduce number of published business profiles for testing
4. Check for plugin conflicts
5. Enable object caching (Redis/Memcached)

### Mobile Layout Issues
**Issue**: Search interface broken on mobile

**Solutions**:
1. Clear browser cache
2. Verify responsive CSS is loaded
3. Check viewport meta tag in theme
4. Test in device simulator
5. Check for theme CSS conflicts

## Future Enhancements

### Potential Features
- [ ] Search history/recent searches
- [ ] Search suggestions/autocomplete
- [ ] Advanced search filters (price range, location)
- [ ] Search analytics (popular terms)
- [ ] Fuzzy search matching
- [ ] Search result sorting options
- [ ] Save search feature
- [ ] Email alerts for new businesses matching search

### Performance Improvements
- [ ] Elasticsearch integration for large datasets
- [ ] Search result caching
- [ ] Lazy loading of search results
- [ ] Virtual scrolling for large result sets

### UX Improvements
- [ ] Search highlighting in results
- [ ] Voice search support
- [ ] Search result previews on hover
- [ ] Quick filters in dropdown
- [ ] Search result export

## Support

For issues or questions about the search feature:
1. Check this documentation
2. Review code comments in source files
3. Check WordPress debug log for errors
4. Submit issue on GitHub repository

## Changelog

### Version 1.0.0 (December 12, 2025)
- ✅ Initial implementation of AJAX live search
- ✅ Search by name, category, services, rating
- ✅ Live results dropdown interface
- ✅ Integration with existing filters
- ✅ Responsive design for mobile
- ✅ Full security implementation
- ✅ Performance optimization with debouncing
