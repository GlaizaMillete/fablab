<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Control Room</title>
</head>

<body>
    <div class="container">
        <div class="container-left">
            <div class="header">
                <img src="path/to/your/image.jpg" alt="Description of image" class="admin-image">

            </div>
            <div class="user-content">
                <div class="button" onclick="location.href='#'">
                    <p>Users</p>
                </div>
                <div class="button" onclick="location.href='#'">
                    <p>Logs</p>
                </div>
            </div>
        </div>
        <div class="container-right">
            <div class="full-width-container">
                <div class="title-container">
                    <h1>Users</h1>
                </div>
                <div class="add-button-container">
                    <p>Add</p>
                </div>
            </div>
            <div class="contents">
                <div class="contents-box">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>
                                    <button>Edit</button>
                                    <button>Delete</button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>
                                    <button>Edit</button>
                                    <button>Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>