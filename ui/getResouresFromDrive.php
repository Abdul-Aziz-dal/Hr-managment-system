                <div class="card mt-4 rounded-0">
                    <div class="card-header  bg-primary text-white">
                        <h5>Google Drive Files</h5>
                    </div>
                   
                    <div class="card-body">
                        <?php 
                        $resources = $gdriveAPI->getResources();
                        if (isset($resources['files']) && !empty($resources['files'])) {
                            echo '<div class="row">';
                            foreach ($resources['files'] as $file) {
                                // Get file details
                                $name = htmlspecialchars($file['name']);
                                $mimeType = htmlspecialchars($file['mimeType']);
                                $id = htmlspecialchars($file['id']);

                                // Determine file icon based on MIME type
                                $icon = '';
                                if (str_contains($mimeType, 'image')) {
                                    $icon = 'bi-file-image';
                                } elseif (str_contains($mimeType, 'text')) {
                                    $icon = 'bi-file-text';
                                } elseif (str_contains($mimeType, 'pdf')) {
                                    $icon = 'bi-file-earmark-pdf';
                                } elseif (str_contains($mimeType, 'folder')) {
                                    $icon = 'bi-folder';
                                } elseif (str_contains($mimeType, 'zip')) {
                                    $icon = 'bi-file-zip';
                                } else {
                                    $icon = 'bi-file';
                                }

                                // Render file card
                                echo '
                                    <div class="col-md-4 mb-3">
                                        <div class="card shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="bi ' . $icon . ' display-4 text-secondary"></i>
                                                <h6 class="mt-3">' . $name . '</h6>
                                                <p class="text-muted small">' . $mimeType . '</p>
                                                <a href="https://drive.google.com/file/d/' . $id . '/view" 
                                                    target="_blank" 
                                                    class="btn btn-sm btn-primary">
                                                    View File
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                ';
                            }
                            echo '</div>';
                        } else {
                            echo '<p>No files found.</p>';
                        }
                        ?>
                    </div>
                </div>