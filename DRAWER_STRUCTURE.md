# Blog Management Drawer - Structure View

## Overview
Full-screen drawer with 2-column grid layout for blog post management, featuring forms on the left and activity history on the right.

---

## Drawer Layout

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│  📊 Blog Management                                            [X] Close         │
│  Forms & Activity Dashboard                                                      │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                   │
│  ┌────────────────────────────────┬───────────────────────────────────────────┐ │
│  │  LEFT COLUMN (Forms)           │  RIGHT COLUMN (History)                   │ │
│  │  Grid Column 1                 │  Grid Column 2                            │ │
│  ├────────────────────────────────┼───────────────────────────────────────────┤ │
│  │                                │                                           │ │
│  │  ┌──────────────────────────┐  │  ┌─────────────────────────────────────┐ │ │
│  │  │ 📄 Add Notes             │  │  │ 📈 Activity History           [⋮]  │ │ │
│  │  ├──────────────────────────┤  │  ├─────────────────────────────────────┤ │ │
│  │  │                          │  │  │                                     │ │ │
│  │  │ Note Content:            │  │  │ ✅ Blog post published              │ │ │
│  │  │ ┌────────────────────┐   │  │  │    Post was published and is now... │ │ │
│  │  │ │ [Textarea]         │   │  │  │    2 hours ago                      │ │ │
│  │  │ │                    │   │  │  │                                     │ │ │
│  │  │ │                    │   │  │  │ ✏️ Content updated                  │ │ │
│  │  │ └────────────────────┘   │  │  │    Main content section was...      │ │ │
│  │  │                          │  │  │    4 hours ago                      │ │ │
│  │  │ Note Type: [Dropdown ▼]  │  │  │                                     │ │ │
│  │  │                          │  │  │ 🖼️ Featured image changed          │ │ │
│  │  │ Priority:  [Dropdown ▼]  │  │  │    New featured image uploaded      │ │ │
│  │  │                          │  │  │    1 day ago                        │ │ │
│  │  │                          │  │  │                                     │ │ │
│  │  │ [Clear] [Add Note ✓]     │  │  │ 📝 Draft created                    │ │ │
│  │  └──────────────────────────┘  │  │    Initial draft of the blog post   │ │ │
│  │                                │  │    3 days ago                       │ │ │
│  │  ┌──────────────────────────┐  │  └─────────────────────────────────────┘ │ │
│  │  │ 🕐 Schedule Follow-ups   │  │                                           │ │
│  │  ├──────────────────────────┤  │  ┌─────────────────────────────────────┐ │ │
│  │  │                          │  │  │ 💬 Chat History               [⋮]  │ │ │
│  │  │ Follow-up Title:         │  │  ├─────────────────────────────────────┤ │ │
│  │  │ [Text Input]             │  │  │                                     │ │ │
│  │  │                          │  │  │ 👤 John Doe                         │ │ │
│  │  │ Follow-up Date & Time:   │  │  │ Can we review this before...        │ │ │
│  │  │ [DateTime Picker]        │  │  │ 1 hour ago                          │ │ │
│  │  │                          │  │  │                                     │ │ │
│  │  │ Type:                    │  │  │ 👤 Jane Smith                       │ │ │
│  │  │ [Select Type ▼]          │  │  │ I made some edits to the intro...   │ │ │
│  │  │                          │  │  │ 3 hours ago                         │ │ │
│  │  │ Description:             │  │  │                                     │ │ │
│  │  │ ┌────────────────────┐   │  │  │ 👤 John Doe                         │ │ │
│  │  │ │ [Textarea]         │   │  │  │ Looks great! Just need to...        │ │ │
│  │  │ └────────────────────┘   │  │  │ 5 hours ago                         │ │ │
│  │  │                          │  │  │                                     │ │ │
│  │  │ ☑ Send Email Reminder    │  │  │ 🤖 System                           │ │ │
│  │  │                          │  │  │ Draft auto-saved successfully.      │ │ │
│  │  │ [Clear]                  │  │  │ 2 days ago                          │ │ │
│  │  │ [Schedule Follow-up ✓]   │  │  │                                     │ │ │
│  │  └──────────────────────────┘  │  └─────────────────────────────────────┘ │ │
│  │                                │                                           │ │
│  │  ┌──────────────────────────┐  │                                           │ │
│  │  │ 💬 New Chat Message      │  │                                           │ │
│  │  ├──────────────────────────┤  │                                           │ │
│  │  │                          │  │                                           │ │
│  │  │ Message:                 │  │                                           │ │
│  │  │ ┌────────────────────┐   │  │                                           │ │
│  │  │ │ Type your message  │   │  │                                           │ │
│  │  │ │ here...            │   │  │                                           │ │
│  │  │ └────────────────────┘   │  │                                           │ │
│  │  │                          │  │                                           │ │
│  │  │ ☐ Internal Only          │  │                                           │ │
│  │  │                          │  │                                           │ │
│  │  │ [Clear]                  │  │                                           │ │
│  │  │ [Send Message 📤]        │  │                                           │ │
│  │  └──────────────────────────┘  │                                           │ │
│  │                                │                                           │ │
│  └────────────────────────────────┴───────────────────────────────────────────┘ │
│                                                                                   │
├─────────────────────────────────────────────────────────────────────────────────┤
│  [Close]                                            [Save All Changes]           │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## Component Hierarchy

### Drawer (Fullscreen)
- **Width**: 100vw
- **Height**: 100vh
- **Anchor**: Right
- **Backdrop**: Yes

#### Header
- **Title**: Blog Management
- **Subtitle**: Forms & Activity Dashboard
- **Icon**: dashboard

#### Content (Main Layout)
- **Type**: Layout
- **Grid Template**: 2 columns (1fr 1fr)

##### Column 1: Forms (Left)
- **Grid Column**: 1
- **Layout**: Vertical stack with spacing

**Card 1: Add Notes**
- Icon: 📄 filetext
- Collapsible: Yes (expanded by default)
- Fields:
  - `note_content` (textarea, 4 rows, required, max 1000 chars)
  - `note_type` (select: general, important, todo, feedback)
  - `note_priority` (select: low, medium, high)
- Actions:
  - Clear button (outlined, secondary)
  - Add Note button (primary, with check icon)

**Card 2: Schedule Follow-ups**
- Icon: 🕐 clock
- Collapsible: Yes (expanded by default)
- Fields:
  - `followup_title` (text, required, max 200 chars)
  - `followup_date` (datetime, required)
  - `followup_type` (select: review, update, publish, other)
  - `followup_description` (textarea, 3 rows)
  - `send_reminder` (checkbox, default: true)
- Actions:
  - Clear button (outlined, secondary)
  - Schedule Follow-up button (primary, with check icon)

**Card 3: New Chat Message**
- Icon: 💬 messagecircle
- Collapsible: Yes (expanded by default)
- Fields:
  - `message` (textarea, 3 rows, required, max 500 chars)
  - `internal_only` (checkbox, default: false)
- Actions:
  - Clear button (outlined, secondary)
  - Send Message button (primary, with send icon)

##### Column 2: History (Right)
- **Grid Column**: 2
- **Layout**: Vertical stack with spacing
- **Overflow**: Auto scroll

**Card 1: Activity History**
- Icon: 📈 activity
- Collapsible: Yes (expanded by default)
- Max Height: 500px
- Header Action: More button (⋮)
- Component: Timeline
  - Vertical orientation
  - Shows timestamps
  - Shows icons
  - Events:
    1. Blog post published (2 hours ago) - ✅ success
    2. Content updated (4 hours ago) - ℹ️ info
    3. Featured image changed (1 day ago) - ⚠️ warning
    4. Draft created (3 days ago) - 📝 default

**Card 2: Chat History**
- Icon: 💬 messagecircle
- Collapsible: Yes (expanded by default)
- Max Height: 500px
- Header Action: More button (⋮)
- Component: List
  - Shows avatars
  - Shows timestamps
  - Messages:
    1. John Doe - "Can we review this before publishing?" (1 hour ago)
    2. Jane Smith - "I made some edits to the introduction." (3 hours ago)
    3. John Doe - "Looks great! Just need to update the images." (5 hours ago)
    4. System - "Draft auto-saved successfully." (2 days ago)

#### Footer
- Close button (outlined, secondary)
- Save All Changes button (primary, with check icon)

---

## API Endpoints

### Form Actions
- **Add Notes**: `POST /api/blogs/:id/notes`
- **Schedule Follow-up**: `POST /api/blogs/:id/followups`
- **Send Message**: `POST /api/blogs/:id/chats`

### Data Loading
- **Blog Data**: `GET /api/blogs/:id`
- **Activity History**: `GET /api/blogs/:id/activity`
- **Chat History**: `GET /api/blogs/:id/chats`

---

## Form Classes

### BlogNotesForm.php
Location: `app/Forms/Blog/BlogNotesForm.php`
- Fields: note_content, note_type, note_priority
- Buttons: Clear, Add Note

### BlogFollowUpsForm.php
Location: `app/Forms/Blog/BlogFollowUpsForm.php`
- Fields: followup_title, followup_date, followup_type, followup_description, send_reminder
- Buttons: Clear, Schedule Follow-up

### BlogChatForm.php
Location: `app/Forms/Blog/BlogChatForm.php`
- Fields: message, internal_only
- Buttons: Clear, Send Message

---

## Layout Class

### BlogLayout.php
Location: `app/Layouts/BlogLayout.php`

#### Key Methods
- `buildViewBlogFormActivityDrawer()` - Main drawer builder
- `buildAddNotesFormCard()` - Notes form card
- `buildFollowUpsFormCard()` - Follow-ups form card
- `buildChatInputFormCard()` - Chat input form card
- `buildActivityHistoryCard()` - Activity timeline card
- `buildChatHistoryCard()` - Chat history list card

---

## Package Components Used

### Litepie Layout Package
- `LayoutBuilder` - Main layout builder
- `DrawerComponent` - Drawer container
- `GridSection` - 2-column grid layout
- `CardComponent` - Card containers
- `TimelineComponent` - Activity timeline
- `ListComponent` - Chat message list

### Litepie Form Package
- `FormComponent` - Form container
- Field types: textarea, select, text, datetime, checkbox
- Form actions (buttons)

---

## Features

### Forms
✅ Submit and Clear buttons on all forms
✅ Field validation (required, max length)
✅ Help text and placeholders
✅ Default values
✅ Icons for visual appeal

### History Cards
✅ More button (⋮) for additional options
✅ Collapsible cards
✅ Scrollable content (max-height: 500px)
✅ Timestamps on all entries
✅ Icons and color coding
✅ Avatar display in chat

### Layout
✅ Responsive 2-column grid
✅ Fullscreen mode (100vw x 100vh)
✅ Backdrop with close-on-escape
✅ Header with title and icon
✅ Footer with action buttons
✅ Proper spacing and gaps

---

## Access

### Route
```
GET /layouts/blog/drawer/view-blog-form-activity-drawer-fullscreen
```

### Response Format
```json
{
  "success": true,
  "data": {
    "type": "drawer",
    "name": "view-blog-form-activity-drawer",
    "anchor": "right",
    "width": "100vw",
    "height": "100vh",
    "header": {...},
    "content": {...},
    "footer": {...}
  }
}
```
