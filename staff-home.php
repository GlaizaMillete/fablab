<?php $pageTitle = "Job Requests"; ?>
<?php include 'header.php'; ?>

    <div class="container">
        <div class="container-left">
            <div class="header">
                <img src="path/to/your/image.jpg" alt="Description of image" class="admin-image">

            </div>
            <div class="user-content">
                <div class="button" onclick="location.href='#'">
                    <p>Job Requests</p>
                </div>
            </div>
        </div>
        <div class="container-right">
            <div class="full-width-container">
                <div class="title-container">
                    <h1>Job Requests</h1>
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
                                <th>Job ID</th>
                                <th>Job Service</th>
                                <th>Client</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>20250305</td>
                                <td>3D Printing</td>
                                <td>John Doe</td>
                                <td>Pending</td>
                            </tr>
                            <tr>
                                <td>20250307</td>
                                <td>Laser Cutting</td>
                                <td>Jane Smith</td>
                                <td>Completed</td>
                            </tr>
                            <tr>
                                <td>20250306</td>
                                <td>CNC Milling</td>
                                <td>Bob Johnson</td>
                                <td>In Progress</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>