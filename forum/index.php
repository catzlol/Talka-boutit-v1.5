<?php
include("../SiT_3/config.php");
include("../SiT_3/header.php");

// Set the number of items per page
$itemsPerPage = 10;

// Get the current page number from the URL, defaulting to 1 if not set
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting offset
$offset = ($page - 1) * $itemsPerPage;

// Handle search
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $searchCondition = "AND `name` LIKE '%$search%'";
} else {
    $searchCondition = "";
}

// Get the total number of forum boards
$totalItemsQuery = "SELECT COUNT(*) as total FROM `forum_boards` WHERE `id` >= 1 $searchCondition";
$totalItemsResult = $conn->query($totalItemsQuery);
$totalItemsRow = $totalItemsResult->fetch_assoc();
$totalItems = $totalItemsRow['total'];

// Calculate the total number of pages
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch the forum boards for the current page
$sqlForum = "SELECT * FROM `forum_boards` WHERE `id` >= 1 $searchCondition ORDER BY `id` ASC LIMIT $itemsPerPage OFFSET $offset";
$boardResult = $conn->query($sqlForum);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Forum - Talkaboutit</title>
</head>

<body>
    <div id="body">
        <div id="box" style="text-align:center;margin-bottom:10px;">
            <div id="subsect">
                <form action="" method="GET" style="margin:15px;">
                    <input style="width:500px; height:20px;" type="text" name="search" placeholder="I'm looking for..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <input style="height:24px;" type="submit" value="Submit">
                </form>
            </div>
        </div>

        <table width="100%" cellspacing="1" cellpadding="4" border="0" style="background-color:#000;">
            <tbody>
                <tr>
                    <th width="50%">
                        <p class="title" style="color:#FFF;">Forum</p>
                    </th>
                    <th width="12%">
                        <p class="title" style="color:#FFF;">Topics</p>
                    </th>
                    <th width="12%">
                        <p class="title" style="color:#FFF;">Posts</p>
                    </th>
                    <th width="13%">
                        <p class="title" style="color:#FFF;">Latest Post</p>
                    </th>
                    <?php
                    if ($power >= 1) {
                        echo '<th width="13%"><p class="title" style="color:#FFF;">Maker</p></th>';
                    }
                    ?>
                </tr>

                <?php
                $table = '';

                while ($forumRow = $boardResult->fetch_assoc()) {
                    $forumID = $forumRow['id'];

                    $sqlThread = "SELECT * FROM `forum_threads` WHERE  `board_id` = '$forumID' AND `deleted` = 'no' ORDER BY `latest_post` DESC";
                    $threadResult = $conn->query($sqlThread);
                    $threadRow = $threadResult->fetch_assoc();
                    $threadID = $threadRow['id'];

                    $sqlPost = "SELECT * FROM `forum_posts` WHERE  `thread_id` = '$threadID' ORDER BY `id` DESC";
                    $postResult = $conn->query($sqlPost);
                    $postRow = $postResult->fetch_assoc();
                    if ($postResult->num_rows == 0) {
                        $sqlPost = "SELECT * FROM `forum_threads` WHERE  `id` = '$threadID' ORDER BY `id` DESC";
                        $postResult = $conn->query($sqlPost);
                        $postRow = $postResult->fetch_assoc();
                    }

                    $authorID = $postRow['author_id'];
                    $sqlUser = "SELECT * FROM `beta_users` WHERE  `id` = '$authorID'";
                    $forumUserResult = $conn->query($sqlUser);
                    $forumUserRow = $forumUserResult->fetch_assoc();

                    $topicsSQL = "SELECT * FROM `forum_threads` WHERE `board_id` = '$forumID'";
                    $topicsResult = $conn->query($topicsSQL);

                    $postsSQL = "SELECT * FROM `forum_posts`";
                    $postsResult = $conn->query($postsSQL);
                    $count = 0;
                    while ($postsRow = $postsResult->fetch_assoc()) {
                        $threadParent = $postsRow['thread_id'];
                        $threadsSQL = "SELECT * FROM `forum_threads` WHERE `id` = '$threadParent' AND `board_id` = '$forumID'";
                        $threadsResult = $conn->query($threadsSQL);
                        if ($threadsResult->num_rows != 0) {
                            $count += 1;
                        }
                    }

                    // Escape user input using htmlspecialchars
                    $forumName = htmlspecialchars($forumRow['name'], ENT_QUOTES, 'UTF-8');
                    $forumDescription = htmlspecialchars($forumRow['description'], ENT_QUOTES, 'UTF-8');
                    $threadTitle = htmlspecialchars($threadRow['title'], ENT_QUOTES, 'UTF-8');
                    $threadAuthor = htmlspecialchars($forumUserRow['username'], ENT_QUOTES, 'UTF-8');

                    // Modified lines to use escaped variables
                    $table .= '<tr class="forumColumn">
                        <td>
                            <a class="title" href="board?id=' . $forumID . '">' . $forumName . '</a>
                            <p class="description">' . $forumDescription . '</p>
                        </td>
                        <td style="text-align:center;">
                            <p class="description">' . $topicsResult->num_rows . '</p>
                        </td>
                        <td style="text-align:center;">
                            <p class="description">' . ($count + $topicsResult->num_rows) . '</p>
                        </td>
                        <td>
                            <p class="description"><strong>' . $postRow['date'] . '</strong> in <a class="description" href="thread?id=' . $threadRow['id'] . '"><strong>' . $threadTitle . '</strong></a><br>by <a class="description" href="/user?id=' . $authorID . '"><strong>' . $threadAuthor . '</strong></a></p>
                        </td>';

                    // Check if the user has admin power and display Maker's ID with hyperlink
                    if ($power >= 1) {
                        $makerID = $forumRow['userid'];
                        $table .= '<td style="text-align:center;">
                            <p class="description"><a href="/user?id=' . $makerID . '">' . $makerID . '</a></p>
                        </td>';
                    }

                    $table .= '</tr>';
                }
                echo $table;
                ?>

            </tbody>
        </table>

        <!-- Pagination -->
        <div style="text-align:center; margin: 20px 0;">
            <?php
            if ($totalPages > 1) {
                for ($i = 1; $i <= $totalPages; $i++) {
                    // Highlight the current page number
                    if ($i == $page) {
                        echo "<strong>$i</strong> ";
                    } else {
                        // Add search parameter to pagination links if a search is active
                        $searchQuery = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                        echo "<a href='?page=$i$searchQuery'>$i</a> ";
                    }
                }
            }
            ?>
        </div>

    </div>
    <?php
    include("../SiT_3/footer.php");
    ?>
</body>

</html>
