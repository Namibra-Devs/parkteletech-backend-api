### Postman Documentation

#### 1. **Files**

##### **1.1 List Files**

- **Endpoint**: `GET /api/files`
- **Description**: Retrieve a list of files.
- **Query Parameters**:
  - `search` (optional): Search term to filter files by name.
  - `folder_id` (optional): Filter files by folder ID.
  - `sort_by` (optional): Column to sort by (e.g., `name`).
  - `sort_order` (optional): Sorting order (e.g., `asc` or `desc`).
  - `per_page` (optional): Number of items per page.

- **Response**:
  ```json
  [
      {
          "id": 1,
          "user_id": 1,
          "folder_id": 2,
          "name": "file1.txt",
          "path": "files/file1.txt",
          "created_at": "2024-07-31T12:00:00.000000Z",
          "updated_at": "2024-07-31T12:00:00.000000Z"
      },
      // More files...
  ]
  ```

##### **1.2 Upload File**

- **Endpoint**: `POST /api/files/upload`
- **Description**: Upload a new file.
- **Form Data**:
  - `files[]` (required): File(s) to be uploaded (can send multiple files).
  - `folder_id` (optional): Folder ID to associate with the file.
  - `user_id` (required): User ID associated with the file.

- **Response**:
  ```json
  [
      {
          "name": "file1.txt",
          "path": "external_files/file1.txt"
      },
      // More files...
  ]
  ```

##### **1.3 Rename File**

- **Endpoint**: `PUT /api/files/{id}/rename`
- **Description**: Rename a file.
- **Parameters**:
  - `id` (path): ID of the file to rename.
- **Body**:
  - `name` (required): New name for the file.

- **Response**:
  ```json
  {
      "id": 1,
      "user_id": 1,
      "folder_id": 2,
      "name": "newfile.txt",
      "path": "files/newfile.txt",
      "created_at": "2024-07-31T12:00:00.000000Z",
      "updated_at": "2024-07-31T12:00:00.000000Z"
  }
  ```

##### **1.4 Delete File**

- **Endpoint**: `DELETE /api/files/{id}`
- **Description**: Delete a file.
- **Parameters**:
  - `id` (path): ID of the file to delete.

- **Response**: `204 No Content`

##### **1.5 Bulk Delete Files**

- **Endpoint**: `DELETE /api/files/delete`
- **Description**: Bulk delete files.
- **Body**:
  ```json
  {
      "ids": [1, 2, 3]
  }
  ```

- **Response**: `204 No Content`

#### 2. **Folders**

##### **2.1 List Folders**

- **Endpoint**: `GET /api/folders`
- **Description**: Retrieve a list of folders.
- **Query Parameters**:
  - `user_id` (optional): Filter folders by user ID.

- **Response**:
  ```json
  [
      {
          "id": 1,
          "user_id": 1,
          "name": "Folder1",
          "parent_id": null,
          "created_at": "2024-07-31T12:00:00.000000Z",
          "updated_at": "2024-07-31T12:00:00.000000Z"
      },
      // More folders...
  ]
  ```

##### **2.2 Create Folder**

- **Endpoint**: `POST /api/folders`
- **Description**: Create a new folder.
- **Body**:
  ```json
  {
      "name": "New Folder",
      "parent_id": null,
      "user_id": 1
  }
  ```

- **Response**:
  ```json
  {
      "id": 1,
      "user_id": 1,
      "name": "New Folder",
      "parent_id": null,
      "created_at": "2024-07-31T12:00:00.000000Z",
      "updated_at": "2024-07-31T12:00:00.000000Z"
  }
  ```

##### **2.3 Rename Folder**

- **Endpoint**: `PUT /api/folders/{id}/rename`
- **Description**: Rename a folder.
- **Parameters**:
  - `id` (path): ID of the folder to rename.
- **Body**:
  - `name` (required): New name for the folder.

- **Response**:
  ```json
  {
      "id": 1,
      "user_id": 1,
      "name": "Renamed Folder",
      "parent_id": null,
      "created_at": "2024-07-31T12:00:00.000000Z",
      "updated_at": "2024-07-31T12:00:00.000000Z"
  }
  ```

##### **2.4 Delete Folder**

- **Endpoint**: `DELETE /api/folders/{id}`
- **Description**: Delete a folder.
- **Parameters**:
  - `id` (path): ID of the folder to delete.

- **Response**: `204 No Content`

##### **2.5 Bulk Delete Folders**

- **Endpoint**: `DELETE /api/folders/delete`
- **Description**: Bulk delete folders.
- **Body**:
  ```json
  {
      "ids": [1, 2, 3]
  }
  ```

- **Response**: `204 No Content`

#### 3. **Staff Authentication**

##### **3.1 Register Staff**

- **Endpoint**: `POST /api/staff/register`
- **Description**: Register a new staff member.
- **Body**:
  ```json
  {
      "fullname": "John Doe",
      "email": "john.doe@example.com",
      "password": "password",
      "password_confirmation": "password"
  }
  ```

- **Response**:
  ```json
  {
      "message": "Staff successfully registered.",
      "staff": {
          "id": 1,
          "fullname": "John Doe",
          "email": "john.doe@example.com",
          "api_token": "random-token"
      },
      "token": "random-token"
  }
  ```

##### **3.2 Login Staff**

- **Endpoint**: `POST /api/staff/login`
- **Description**: Authenticate staff member and issue a token.
- **Body**:
  ```json
  {
      "email": "john.doe@example.com",
      "password": "password"
  }
  ```

- **Response**:
  ```json
  {
      "message": "Staff logged in successfully.",
      "staff": {
          "id": 1,
          "fullname": "John Doe",
          "email": "john.doe@example.com",
          "api_token": "random-token"
      },
      "token": "random-token"
  }
  ```

##### **3.3 Logout Staff**

- **Endpoint**: `POST /api/staff/logout`
- **Description**: Invalidate the staff member's token.
- **Headers**:
  - `Authorization: Bearer {token}`

- **Response**:
  ```json
  {
      "message": "Successfully logged out"
  }
  ```

### Summary

1. **Files**
   - `GET /api/files`: List files
   - `POST /api/files/upload`: Upload file(s)
   - `PUT /api/files/{id}/rename`: Rename a file
   - `DELETE /api/files/{id}`: Delete a file
   - `DELETE /api/files/delete`: Bulk delete files

2. **Folders**
   - `GET /api/folders`: List folders
   - `POST /api/folders`: Create a new folder
   - `PUT /api/folders/{id}/rename`: Rename a folder
   - `DELETE /api/folders/{id}`: Delete a folder
   - `DELETE /api/folders/delete`: Bulk delete folders

3. **Staff Authentication**
   - `POST /api/staff/register`: Register a new staff member
   - `POST /api/staff/login`: Login staff member
   - `POST /api/staff/logout`: Logout staff member
