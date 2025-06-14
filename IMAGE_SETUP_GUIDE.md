# 🖼️ Image Setup Guide for Event Booking System

## 📋 Understanding the Image System

The SQL script only contains **image filenames**, not the actual images. You need to place actual image files in the `images/events/` directory.

### Required Image Files:
```
images/events/techconference.jpg
images/events/musicfestival.jpg
images/events/foodwine.png
images/events/business.jpg
images/events/artgallery.avif
```

## 🎯 Option 1: Download Real Images (Best Quality)

### Step 1: Create Directory
Make sure the directory exists:
```
webdev/images/events/
```

### Step 2: Download Images
Visit these free image sites and download images:

#### **Unsplash.com** (Recommended)
- Search: "technology conference" → Save as `techconference.jpg`
- Search: "music festival" → Save as `musicfestival.jpg`
- Search: "food wine tasting" → Save as `foodwine.png`
- Search: "business networking" → Save as `business.jpg`
- Search: "art gallery" → Save as `artgallery.avif` (or .jpg)

#### **Pexels.com**
- High-quality free stock photos
- Same search terms as above

#### **Pixabay.com**
- Free images with good selection
- Download in high resolution

### Step 3: Rename Files
Ensure exact filenames:
- `techconference.jpg`
- `musicfestival.jpg`
- `foodwine.png`
- `business.jpg`
- `artgallery.avif`

### Step 4: Upload to Server
Place files in: `webdev/images/events/`

## 🛠️ Option 2: Use Automatic Image Generator

### Quick Solution - PHP GD Images
Visit: `http://localhost/webdev/create_simple_images.php`

**Features:**
- ✅ Creates actual image files (JPEG/PNG)
- ✅ Uses PHP GD library (built into most PHP installations)
- ✅ Color-coded by event type
- ✅ Shows event name, location, and price
- ✅ Works in all browsers

### Alternative - SVG Images
Visit: `http://localhost/webdev/generate_images.php`

**Features:**
- ✅ Creates SVG files (scalable)
- ✅ Beautiful gradients and styling
- ✅ Smaller file sizes
- ✅ Professional appearance

## 🔍 Option 3: Debug and Test

### Check Current Status
Visit: `http://localhost/webdev/debug_images.php`

**Shows:**
- ✅ Which files exist
- ✅ File sizes and permissions
- ✅ Directory contents
- ✅ Live loading tests

### Test Image Display
Visit: `http://localhost/webdev/test_images.php`

**Features:**
- ✅ Tests each image individually
- ✅ Shows error messages
- ✅ Direct image links

## 📱 Option 4: Use Placeholder Icons (Fallback)

If you don't want to deal with images, you can modify the database to use NULL for images:

```sql
UPDATE events SET image = NULL;
```

This will show nice placeholder icons instead of images.

## 🎨 Recommended Image Specifications

### **File Formats:**
- **JPEG** (.jpg) - Best for photos
- **PNG** (.png) - Best for graphics with transparency
- **AVIF** (.avif) - Modern format, smaller files

### **Dimensions:**
- **Minimum**: 400x300 pixels
- **Recommended**: 800x600 pixels
- **Maximum**: 1200x900 pixels

### **File Size:**
- **Target**: 50-200 KB per image
- **Maximum**: 500 KB per image

### **Quality:**
- High resolution for crisp display
- Good compression to reduce load times

## 🚀 Quick Start (Recommended Steps)

### **For Development/Testing:**
1. Visit: `http://localhost/webdev/create_simple_images.php`
2. Click to generate all images automatically
3. Test: `http://localhost/webdev/`

### **For Production:**
1. Download real images from Unsplash/Pexels
2. Rename to exact filenames required
3. Upload to `images/events/` directory
4. Test with debug page

## 🔧 Troubleshooting

### **Images Not Displaying?**
1. **Check file existence**: Use debug page
2. **Check permissions**: Ensure files are readable
3. **Check file size**: Ensure files aren't 0 bytes
4. **Check browser cache**: Add `?v=timestamp` to URLs

### **Generator Not Working?**
1. **Check PHP GD**: Ensure GD extension is enabled
2. **Check permissions**: Ensure directory is writable
3. **Check disk space**: Ensure enough space available

### **Wrong Image Names?**
The database expects these exact filenames:
- `techconference.jpg` (not tech-conference.jpg)
- `musicfestival.jpg` (not music-festival.jpg)
- `foodwine.png` (not food-wine.png)
- `business.jpg` (not business-networking.jpg)
- `artgallery.avif` (not art-gallery.avif)

## 📊 File Structure

```
webdev/
├── images/
│   └── events/
│       ├── techconference.jpg    (150,000 CFA event)
│       ├── musicfestival.jpg     (85,000 CFA event)
│       ├── foodwine.png          (45,000 CFA event)
│       ├── business.jpg          (25,000 CFA event)
│       └── artgallery.avif       (5,000 CFA event)
├── create_simple_images.php      (Image generator)
├── debug_images.php              (Debug tool)
└── index.php                     (Homepage)
```

## ✅ Success Checklist

- [ ] Directory `images/events/` exists
- [ ] All 5 image files present with correct names
- [ ] Files are not 0 bytes (have actual content)
- [ ] Images display on homepage: `http://localhost/webdev/`
- [ ] No broken image icons visible
- [ ] Events show correct CFA prices (5,000 to 150,000)

---

**🎯 Goal**: Beautiful event images displaying properly on your Cameroon event booking system!

**⏱️ Time**: 5-10 minutes with automatic generator, 15-30 minutes with real images

**🔧 Difficulty**: Easy with generators, moderate with manual download
