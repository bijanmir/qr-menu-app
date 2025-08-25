# Menu Sharing System

This system allows restaurant owners to share menu items between restaurants in two ways:

## 🔄 Duplication (Independent Copies)
- Creates an independent copy of an item
- Can be modified without affecting the original
- Full ownership of the duplicated item
- No ongoing sync relationship

## 🔗 Linking (Synchronized Items)  
- Creates a linked reference to the original item
- Changes to the original item can sync to linked copies
- Configurable sync settings (price, description, image overrides)
- Maintains relationship with source restaurant

## Features

### For Restaurant Owners
- **Browse & Discovery**: Search and browse items from other restaurants in their network
- **Smart Sharing**: Duplicate or link items with granular control
- **Management Interface**: View and manage all shared items in one place
- **Sync Control**: Manual and automatic sync for linked items
- **Permission System**: Control who can share from/to your restaurant

### Sharing Permissions
- **Same Tenant**: Share within the same organization
- **Public**: Allow sharing with any restaurant
- **Auto-Approval**: Automatically approve sharing requests
- **Override Controls**: Allow price/description/image modifications

### Technical Features
- **Audit Trail**: Complete history of all sharing actions
- **Request System**: Approval workflow for sharing requests
- **Background Sync**: Automatic syncing of linked items via command
- **Relationship Tracking**: Track source items and propagate changes

## Database Schema

### Key Tables
- `items` - Extended with sharing fields
- `item_sharing_history` - Audit trail of sharing actions
- `item_sharing_requests` - Approval workflow
- `restaurants` - Extended with sharing settings

### Sharing Types
- `original` - Original item created by restaurant
- `duplicated` - Independent copy from another restaurant  
- `linked` - Synchronized copy that can receive updates

## API Endpoints

### Browsing & Discovery
- `GET /owner/restaurants/{restaurant}/sharing/browse` - Browse available items
- `GET /owner/restaurants/{restaurant}/sharing/item/{item}` - View item details

### Sharing Actions
- `POST /owner/restaurants/{restaurant}/sharing/duplicate` - Duplicate an item
- `POST /owner/restaurants/{restaurant}/sharing/link` - Link an item
- `POST /owner/restaurants/{restaurant}/sharing/request` - Request sharing permission

### Management
- `GET /owner/restaurants/{restaurant}/sharing/manage` - Manage shared items
- `POST /owner/restaurants/{restaurant}/sharing/items/{item}/sync` - Sync linked item
- `POST /owner/restaurants/{restaurant}/sharing/items/{item}/unlink` - Unlink item

### Requests
- `GET /owner/restaurants/{restaurant}/sharing/requests` - View sharing requests
- `POST /owner/restaurants/{restaurant}/sharing/requests/{request}/approve` - Approve request
- `POST /owner/restaurants/{restaurant}/sharing/requests/{request}/reject` - Reject request

## Commands

### Sync Linked Items
```bash
# Sync all linked items
php artisan menu:sync-linked-items

# Dry run to see what would be synced
php artisan menu:sync-linked-items --dry-run

# Sync items for specific restaurant
php artisan menu:sync-linked-items --restaurant=123
```

## Usage Examples

### Basic Duplication
```php
$sharingService = app(MenuSharingService::class);

$duplicatedItem = $sharingService->duplicateItem(
    sourceItem: $originalItem,
    targetMenu: $myMenu, 
    targetCategory: $myCategory,
    user: auth()->user(),
    overrides: ['price' => 15.99, 'name' => 'Our Special Burger']
);
```

### Linking with Sync Settings
```php
$linkedItem = $sharingService->linkItem(
    sourceItem: $originalItem,
    targetMenu: $myMenu,
    targetCategory: $myCategory, 
    user: auth()->user(),
    syncSettings: [
        'allow_price_override' => true,
        'allow_description_override' => false,
        'allow_image_override' => true
    ]
);
```

### Manual Sync
```php
$sharingService->syncLinkedItem($linkedItem, auth()->user());
```

## Sharing Settings

Restaurant sharing settings control how items can be shared:

```php
$restaurant->update([
    'sharing_settings' => [
        'allow_incoming_duplications' => true,
        'allow_incoming_links' => true,
        'allow_outgoing_sharing' => true,
        'auto_approve_sharing' => false,
        'sharing_permissions' => ['same_tenant', 'public']
    ]
]);
```

## Models

### Item Model Extensions
- `isOriginal()`, `isDuplicated()`, `isLinked()`, `isShared()`
- `canOverrideField(string $field)` - Check if field can be overridden
- `getSyncableFields()` - Get fields that sync for linked items
- `needsSync()` - Check if linked item needs syncing

### Restaurant Model Extensions  
- `canShareWith(Restaurant $target)` - Check sharing permissions
- `allowsIncomingDuplications()`, `allowsOutgoingSharing()`, etc.
- Sharing request relationships

## Frontend Components

### Browse Interface (`/owner/sharing/browse`)
- Item discovery with search
- Restaurant filtering 
- Duplicate/Link action buttons
- Modal for sharing configuration

### Management Interface (`/owner/sharing/manage`)
- Filter by sharing type (all/duplicated/linked)
- Sync status indicators
- Bulk actions for linked items
- Sharing history

### Request Management (`/owner/sharing/requests`)
- Incoming/outgoing request tabs
- Approve/reject workflow
- Request details and messaging

This system provides a comprehensive solution for menu item sharing between restaurants while maintaining proper permissions, audit trails, and sync capabilities.
