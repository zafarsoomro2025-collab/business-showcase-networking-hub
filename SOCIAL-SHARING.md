# Social Sharing Buttons Feature

## Overview

Social sharing buttons have been added to single business profile pages, allowing visitors to easily share businesses on Facebook, Twitter, LinkedIn, or copy the direct link.

## Features

### 🔗 Sharing Options

1. **Facebook Share**
   - Opens Facebook share dialog in popup window
   - Pre-filled with business page URL
   - Window size: 600x400px

2. **Twitter Share**
   - Opens Twitter share dialog in popup window
   - Includes text: "Check out [Business Name]"
   - Pre-filled with business page URL
   - Window size: 600x400px

3. **LinkedIn Share**
   - Opens LinkedIn share dialog in popup window
   - Pre-filled with business page URL
   - Window size: 600x600px

4. **Copy Link**
   - Copies business profile URL to clipboard
   - Shows "Copied!" feedback for 2 seconds
   - Modern clipboard API with fallback for older browsers

## Location

The social sharing buttons are displayed on single business profile pages:
- **Position**: Below the business header (title, rating, categories)
- **Above**: Main content (description, services)
- **Layout**: Horizontal row with label + buttons

## Visual Design

### Desktop Layout
```
Share this business:  [Facebook] [Twitter] [LinkedIn] [Copy Link]
```

### Mobile Layout (< 768px)
```
Share this business:
[Facebook] [Twitter]
[LinkedIn] [Copy Link]
```

### Mobile Layout (< 480px)
```
Share this business:
[     Facebook     ]
[     Twitter      ]
[     LinkedIn     ]
[    Copy Link     ]
```

## Button Styles

### Facebook
- **Color**: #1877f2 (Facebook Blue)
- **Hover**: #166fe5 (Darker Blue)
- **Icon**: Facebook logo SVG

### Twitter
- **Color**: #1da1f2 (Twitter Blue)
- **Hover**: #0d8bd9 (Darker Blue)
- **Icon**: Twitter bird SVG

### LinkedIn
- **Color**: #0a66c2 (LinkedIn Blue)
- **Hover**: #004182 (Darker Blue)
- **Icon**: LinkedIn logo SVG

### Copy Link
- **Color**: #5f6368 (Gray)
- **Hover**: #3c4043 (Darker Gray)
- **Copied State**: #4caf50 (Green)
- **Icon**: Link chain SVG

## Technical Implementation

### HTML Structure

**Location**: `templates/single-business-profile.php` (line ~73-140)

```php
<div class="business-social-sharing">
    <div class="sharing-label">
        <span><?php esc_html_e( 'Share this business:', 'business-showcase-networking-hub' ); ?></span>
    </div>
    <div class="sharing-buttons">
        
        <!-- Facebook Share -->
        <a href="#" 
           class="share-button share-facebook" 
           data-share-type="facebook"
           data-url="<?php echo esc_url( get_permalink() ); ?>"
           data-title="<?php echo esc_attr( get_the_title() ); ?>">
            <svg>...</svg>
            <span>Facebook</span>
        </a>
        
        <!-- Additional buttons... -->
        
    </div>
</div>
```

### JavaScript Functions

**Location**: `assets/js/script.js` (line ~430-540)

#### `initSocialSharing()`
- Initializes share button click handlers
- Determines share type from data attributes
- Routes to appropriate sharing method

#### `openShareWindow(url, title, width, height)`
- Opens centered popup window for social sharing
- Parameters: share URL, window title, dimensions
- Centers window on screen

#### `copyToClipboard(text, $btn)`
- Attempts modern Clipboard API first
- Falls back to `document.execCommand('copy')` for older browsers
- Shows success feedback

#### `fallbackCopyToClipboard(text, $btn)`
- Legacy clipboard copy method
- Creates temporary textarea element
- Executes copy command
- Removes temporary element

#### `showCopySuccess($btn)`
- Shows "Copied!" text for 2 seconds
- Adds 'copied' class to button (green background)
- Reverts to original state after timeout

### CSS Styling

**Location**: `assets/css/single-business-profile.css` (line ~123-243)

**Main Classes**:
- `.business-social-sharing` - Container
- `.sharing-label` - "Share this business:" text
- `.sharing-buttons` - Button group container
- `.share-button` - Base button styles
- `.share-facebook`, `.share-twitter`, `.share-linkedin`, `.share-copy` - Platform-specific styles

## Share URLs

### Facebook
```
https://www.facebook.com/sharer/sharer.php?u=[ENCODED_URL]
```

### Twitter
```
https://twitter.com/intent/tweet?url=[ENCODED_URL]&text=[ENCODED_TEXT]
```

### LinkedIn
```
https://www.linkedin.com/sharing/share-offsite/?url=[ENCODED_URL]
```

## Browser Compatibility

### Clipboard API
✅ **Modern Browsers**:
- Chrome 63+
- Firefox 53+
- Safari 13.1+
- Edge 79+

⚠️ **Fallback Method**:
- Uses `document.execCommand('copy')` for older browsers
- Works in IE11, older Safari versions
- Requires user interaction (click event)

### Popup Windows
✅ **All Browsers**:
- `window.open()` supported universally
- Popup blockers may interfere (user must allow)
- Mobile browsers may open in new tab instead of popup

## User Experience

### Interaction Flow

**Facebook/Twitter/LinkedIn**:
1. User clicks share button
2. Popup window opens (600x400px or 600x600px)
3. Social platform's share dialog loads
4. User completes sharing on platform
5. Popup closes (user closes or auto-closes)

**Copy Link**:
1. User clicks "Copy Link" button
2. URL is copied to clipboard
3. Button text changes: "Copy Link" → "Copied!"
4. Button color changes: Gray → Green
5. After 2 seconds, reverts to original state

### Visual Feedback

**Hover Effects**:
- Buttons lift slightly (translateY -2px)
- Drop shadow increases
- Background color darkens

**Active/Click**:
- Button returns to original position
- Provides tactile click feedback

**Copy Success**:
- Text changes to "Copied!"
- Background changes to green (#4caf50)
- Temporary state for 2 seconds

## Accessibility

### Keyboard Navigation
- ✅ All buttons are keyboard accessible (Tab key)
- ✅ Enter/Space keys trigger share action
- ✅ Focus indicators visible

### Screen Readers
- ✅ `aria-label` attributes on all buttons
- ✅ Descriptive text: "Share on Facebook", etc.
- ✅ Icon SVGs have proper markup
- ✅ Copy success announced (text change)

### WCAG Compliance
- ✅ Color contrast meets AA standards
- ✅ Focus indicators visible
- ✅ Touch targets min 44x44px on mobile
- ✅ Semantic HTML structure

## Mobile Optimization

### Responsive Breakpoints

**768px and below**:
- Buttons stack in 2-column grid (2 buttons per row)
- Full width container
- Label on separate line

**480px and below**:
- Buttons stack vertically (1 per row)
- Full width buttons
- Increased padding for touch targets

### Touch Optimization
- Minimum touch target: 44x44px
- Adequate spacing between buttons (12px gap)
- No hover effects on touch devices (uses :hover with caution)

## Security

### URL Encoding
- All URLs properly encoded with `encodeURIComponent()`
- Prevents XSS through URL manipulation
- Safe for special characters

### Window Parameters
- Popup windows have restricted permissions
- No toolbar, location bar, or menu bar
- Scrollbars and resize enabled for usability

### External Links
- `rel="noopener noreferrer"` on share links
- Prevents `window.opener` access
- Protects against tabnabbing attacks

## Performance

### Loading
- Inline SVG icons (no external image requests)
- Minimal CSS (< 5KB)
- JavaScript only loads on single business pages
- No external API calls required

### Execution
- Event delegation on button clicks
- No continuous polling or timers (except 2s timeout)
- Clipboard API is synchronous/fast

## Customization

### Change Button Colors

```css
/* In single-business-profile.css */
.share-facebook {
    background: #your-color;
}
```

### Add New Platform

1. **Add HTML button** in template:
```php
<a href="#" 
   class="share-button share-platform" 
   data-share-type="platform"
   data-url="<?php echo esc_url( get_permalink() ); ?>">
    <svg>...</svg>
    <span>Platform</span>
</a>
```

2. **Add JavaScript handler** in `initSocialSharing()`:
```javascript
case 'platform':
    shareUrl = 'https://platform.com/share?url=' + url;
    openShareWindow(shareUrl, 'Platform', 600, 400);
    break;
```

3. **Add CSS styles**:
```css
.share-platform {
    background: #platform-color;
}
```

### Change Popup Window Size

```javascript
// In openShareWindow() function
openShareWindow(shareUrl, 'Facebook', 800, 600);  // Larger window
```

### Change Copy Success Duration

```javascript
// In showCopySuccess() function
setTimeout(function() {
    // Revert...
}, 3000);  // 3 seconds instead of 2
```

## Troubleshooting

### Buttons Not Appearing
**Issue**: Social sharing buttons don't show up

**Solutions**:
1. Verify you're on a single business profile page
2. Check if `single-business-profile.css` is loaded
3. Inspect browser console for JavaScript errors
4. Clear browser cache

### Copy Not Working
**Issue**: Copy link button doesn't copy URL

**Solutions**:
1. Check browser clipboard permissions
2. Verify HTTPS (clipboard API requires secure context)
3. Test fallback method in older browsers
4. Check browser console for errors

### Popup Blocked
**Issue**: Share popup doesn't open

**Solutions**:
1. Check browser popup blocker settings
2. Whitelist your domain in popup blocker
3. Ensure click event (not automatic trigger)
4. Test in different browsers

### Mobile Layout Issues
**Issue**: Buttons overlap or display incorrectly

**Solutions**:
1. Verify responsive CSS is loaded
2. Check viewport meta tag exists
3. Test in device emulator
4. Clear mobile browser cache

## Analytics Integration

### Track Share Events (Optional)

Add to `initSocialSharing()` function:

```javascript
// After successful share
if (typeof gtag !== 'undefined') {
    gtag('event', 'share', {
        'method': shareType,
        'content_type': 'business_profile',
        'content_id': '<?php echo get_the_ID(); ?>'
    });
}

// Or for Google Analytics 4
if (typeof ga !== 'undefined') {
    ga('send', 'social', shareType, 'share', url);
}
```

## Future Enhancements

### Potential Features
- [ ] WhatsApp share button (mobile only)
- [ ] Email share button
- [ ] Pinterest pin button (if business has images)
- [ ] Print button
- [ ] QR code generator for sharing
- [ ] Share count display (requires API integration)
- [ ] Custom share text/hashtags per platform
- [ ] Share tracking/analytics dashboard

### Advanced Options
- [ ] Custom share images (Open Graph tags)
- [ ] Share preview customization
- [ ] A/B testing different button styles
- [ ] Share incentives (rewards for sharing)

## Support

For issues with social sharing buttons:
1. Check this documentation
2. Verify browser console for errors
3. Test in incognito/private mode
4. Submit issue on GitHub with:
   - Browser/version
   - Steps to reproduce
   - Console errors (if any)

## Changelog

### Version 1.0.0 (December 12, 2025)
- ✅ Initial implementation of social sharing buttons
- ✅ Facebook, Twitter, LinkedIn share
- ✅ Copy link to clipboard functionality
- ✅ Responsive design for mobile
- ✅ Accessibility features (ARIA labels, keyboard nav)
- ✅ Modern clipboard API with fallback
- ✅ Visual feedback for copy success
- ✅ Popup window centering
- ✅ Complete styling with brand colors
