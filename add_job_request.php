<?php $pageTitle = "Add Job Request"; ?>
<?php include 'header.php'; ?>

<style>
    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .form-group textarea {
        resize: vertical;
    }

    button[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #45a049;
    }
</style>

<div class="container">
    <div class="container-left">
        <div class="header">
            <img src="FABLAB_LOGO.png" alt="Description of image" class="admin-image">
        </div>
        <div class="user-content">
            <div class="button" onclick="location.href='#'">
                <p>Add Job Request</p>
            </div>
        </div>
    </div>
    <div class="container-right">
        <div class="full-width-container">
            <div class="title-container">
                <h1>Add Job Request</h1>
            </div>
            <!-- <div class="add-button-container">
                <p>Add</p>
            </div> -->
        </div>
        <div class="contents">
            <div class="contents-box">
                <form action="submit_job_request.php" method="post">
                    <div class="form-group">
                        <label for="job_service">Job Service:</label>
                        <input type="text" id="job_service" name="job_service" required>
                    </div>
                    <div class="form-group">
                        <label for="client">Client:</label>
                        <input type="text" id="client" name="client" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>

</html>