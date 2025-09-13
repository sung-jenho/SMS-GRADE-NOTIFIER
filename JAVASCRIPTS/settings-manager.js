document.addEventListener('DOMContentLoaded', function() {
    // Handle modern nav tab switching
    const modernNavItems = document.querySelectorAll('.modern-nav-item');
    modernNavItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            modernNavItems.forEach(navItem => navItem.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');
        });
    });
    // Profile picture upload handling
    const modernProfilePictureContainer = document.querySelector('.modern-profile-picture-container');
    const profilePictureInput = document.getElementById('profilePicture');
    const profilePreview = document.getElementById('profilePreview');

    if (modernProfilePictureContainer && profilePictureInput) {
        modernProfilePictureContainer.addEventListener('click', function() {
            profilePictureInput.click();
        });

        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Admin Profile Form
    const adminProfileForm = document.getElementById('adminProfileForm');
    if (adminProfileForm) {
        adminProfileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                showNotification('Password mismatch', 'error', 'New password and confirm password do not match.');
                return;
            }
            
            showLoadingModal('Updating Profile...');
            
            const formData = new FormData(this);
            
            // Force 3-second delay like login animation
            setTimeout(() => {
                fetch('update_admin_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingModal();
                    
                    if (data.success) {
                        showNotification('Success', 'success', data.message);
                        
                        // Update profile picture preview if new one was uploaded
                        if (data.profile_picture_url) {
                            const preview = document.getElementById('profilePreview');
                            if (preview) {
                                preview.src = data.profile_picture_url + '?v=' + new Date().getTime();
                            }
                            
                            // Update header and sidebar avatars with cache busting
                            const headerAvatar = document.querySelector('.user-avatar-img');
                            const sidebarAvatar = document.querySelector('.sidebar-avatar-img');
                            if (headerAvatar) headerAvatar.src = data.profile_picture_url + '?v=' + new Date().getTime();
                            if (sidebarAvatar) sidebarAvatar.src = data.profile_picture_url + '?v=' + new Date().getTime();
                        }
                        
                        // Update displayed name and email in sidebar
                        const fullName = document.getElementById('fullName').value;
                        const email = document.getElementById('email').value;
                        
                        const sidebarName = document.querySelector('.user-name');
                        const sidebarEmail = document.querySelector('.user-email');
                        if (sidebarName) sidebarName.textContent = fullName;
                        if (sidebarEmail) sidebarEmail.textContent = email;
                        
                        // Clear password fields
                        document.getElementById('currentPassword').value = '';
                        document.getElementById('newPassword').value = '';
                        document.getElementById('confirmPassword').value = '';
                    } else {
                        showNotification('Error', 'error', data.message);
                    }
                })
                .catch(error => {
                    hideLoadingModal();
                    showNotification('Error', 'error', 'An error occurred while updating profile');
                    console.error('Error:', error);
                });
            }, 3000);
        });
    }

    // System Configuration Form
    const systemConfigForm = document.getElementById('systemConfigForm');
    if (systemConfigForm) {
        systemConfigForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            showLoadingModal('Saving Configuration', 'Please wait while we save your system configuration...');
            
            fetch('../PHP/update_system_config.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingModal();
                if (data.success) {
                    showNotification('Configuration Saved', 'success', 'System configuration has been updated successfully.');
                } else {
                    showNotification('Save Failed', 'error', data.message || 'Failed to save configuration.');
                }
            })
            .catch(error => {
                hideLoadingModal();
                console.error('Error:', error);
                showNotification('Save Failed', 'error', 'An error occurred while saving configuration.');
            });
        });
    }

    // SMS Templates Save
    const saveTemplatesBtn = document.getElementById('saveTemplatesBtn');
    if (saveTemplatesBtn) {
        saveTemplatesBtn.addEventListener('click', function() {
            const templates = [];
            document.querySelectorAll('.template-item').forEach(item => {
                const templateId = item.querySelector('.message-template').dataset.templateId;
                const messageTemplate = item.querySelector('.message-template').value;
                const isActive = item.querySelector('.template-active').checked;
                
                templates.push({
                    id: templateId,
                    message_template: messageTemplate,
                    is_active: isActive
                });
            });
            
            showLoadingModal('Saving Templates', 'Please wait while we save your SMS templates...');
            
            fetch('../PHP/update_sms_templates.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ templates: templates })
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingModal();
                if (data.success) {
                    showNotification('Templates Saved', 'success', 'SMS templates have been updated successfully.');
                } else {
                    showNotification('Save Failed', 'error', data.message || 'Failed to save templates.');
                }
            })
            .catch(error => {
                hideLoadingModal();
                console.error('Error:', error);
                showNotification('Save Failed', 'error', 'An error occurred while saving templates.');
            });
        });
    }

    // Database Backup
    const createBackupBtn = document.getElementById('createBackupBtn');
    if (createBackupBtn) {
        createBackupBtn.addEventListener('click', function() {
            showLoadingModal('Creating Backup', 'Please wait while we create a database backup...');
            
            fetch('../PHP/create_backup.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingModal();
                if (data.success) {
                    showNotification('Backup Created', 'success', 'Database backup has been created successfully.');
                    loadBackupsList();
                } else {
                    showNotification('Backup Failed', 'error', data.message || 'Failed to create backup.');
                }
            })
            .catch(error => {
                hideLoadingModal();
                console.error('Error:', error);
                showNotification('Backup Failed', 'error', 'An error occurred while creating backup.');
            });
        });
    }

    // Database Restore
    const restoreBackupBtn = document.getElementById('restoreBackupBtn');
    const restoreFile = document.getElementById('restoreFile');
    if (restoreBackupBtn && restoreFile) {
        restoreBackupBtn.addEventListener('click', function() {
            restoreFile.click();
        });

        restoreFile.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                showRestoreConfirmation(() => {
                    const formData = new FormData();
                    formData.append('backup_file', file);
                    
                    showLoadingModal('Restoring Database', 'Please wait while we restore the database...');
                    
                    fetch('../PHP/restore_backup.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoadingModal();
                        if (data.success) {
                            showNotification('Database Restored', 'success', 'Database has been restored successfully.');
                        } else {
                            showNotification('Restore Failed', 'error', data.message || 'Failed to restore database.');
                        }
                    })
                    .catch(error => {
                        hideLoadingModal();
                        console.error('Error:', error);
                        showNotification('Restore Failed', 'error', 'An error occurred while restoring database.');
                    });
                });
            }
        });
    }

    // Data Export
    const exportBtns = document.querySelectorAll('.export-btn');
    exportBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            const format = this.dataset.format;
            
            showLoadingModal('Generating Report', `Please wait while we generate your ${format.toUpperCase()} report...`);
            
            fetch('../PHP/export_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ type: type, format: format })
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Export failed');
            })
            .then(blob => {
                hideLoadingModal();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `${type}_report_${new Date().toISOString().split('T')[0]}.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                showNotification('Export Complete', 'success', 'Your report has been downloaded successfully.');
            })
            .catch(error => {
                hideLoadingModal();
                console.error('Error:', error);
                showNotification('Export Failed', 'error', 'An error occurred while generating the report.');
            });
        });
    });

    // Load backups list
    function loadBackupsList() {
        fetch('../PHP/list_backups.php')
            .then(response => response.json())
            .then(data => {
                const backupsList = document.getElementById('backupsList');
                if (backupsList && data.success) {
                    backupsList.innerHTML = '';
                    data.backups.forEach(backup => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td style="padding: 1rem; color: #374151; font-weight: 500;">${backup.filename}</td>
                            <td style="padding: 1rem; color: #6b7280;">${backup.size}</td>
                            <td style="padding: 1rem; color: #6b7280;">${backup.created}</td>
                            <td style="padding: 1rem; text-align: center;">
                                <button class="modern-action-btn download-backup" data-filename="${backup.filename}" 
                                        style="background: #3b82f6; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; margin-right: 0.5rem; cursor: pointer; transition: all 0.3s ease;">
                                    <i class="bi bi-download me-1"></i>Download
                                </button>
                                <button class="modern-action-btn delete-backup" data-filename="${backup.filename}"
                                        style="background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </td>
                        `;
                        backupsList.appendChild(row);
                    });

                    // Add event listeners for download and delete buttons
                    backupsList.querySelectorAll('.download-backup').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const filename = this.dataset.filename;
                            window.open(`../PHP/download_backup.php?file=${encodeURIComponent(filename)}`, '_blank');
                        });
                    });

                    backupsList.querySelectorAll('.delete-backup').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const filename = this.dataset.filename;
                            showDeleteConfirmation(filename, () => {
                                showLoadingModal('Deleting Backup', 'Please wait while we delete the backup file...');
                                fetch('../PHP/delete_backup.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({ filename: filename })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    hideLoadingModal();
                                    if (data.success) {
                                        showNotification('Backup Deleted', 'success', 'Backup file has been deleted successfully.');
                                        loadBackupsList();
                                    } else {
                                        showNotification('Delete Failed', 'error', data.message || 'Failed to delete backup.');
                                    }
                                })
                                .catch(error => {
                                    hideLoadingModal();
                                    console.error('Error:', error);
                                    showNotification('Delete Failed', 'error', 'An error occurred while deleting backup.');
                                });
                            });
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Error loading backups:', error);
            });
    }

    // Helper functions
    let loadingAnimation = null;
    
    function showLoadingModal(message) {
        const loadingModal = document.getElementById('loadingModal');
        const loadingMessage = document.getElementById('loadingMessage');
        loadingMessage.textContent = message;
        loadingModal.classList.add('show');
        
        // Initialize Lottie loading animation
        if (window.lottie && !loadingAnimation) {
            try {
                loadingAnimation = window.lottie.loadAnimation({
                    container: document.getElementById('loadingAnimation'),
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: '../assets/loading.json'
                });
            } catch (e) {
                console.log('Could not load Lottie animation');
            }
        } else if (loadingAnimation) {
            loadingAnimation.play();
        }
    }

    function hideLoadingModal() {
        const loadingModal = document.getElementById('loadingModal');
        loadingModal.classList.remove('show');
        
        // Stop Lottie animation
        if (loadingAnimation) {
            loadingAnimation.stop();
        }
    }

    function showNotification(title, type, message) {
        // Use existing notification system if available
        if (typeof window.showNotification === 'function') {
            window.showNotification(title, type, message);
        } else {
            // Fallback alert
            alert(`${title}: ${message}`);
        }
    }

    // Modern confirmation dialogs
    function showDeleteConfirmation(filename, onConfirm) {
        const modal = createConfirmationModal(
            'Delete Backup',
            `Are you sure you want to delete "${filename}"? This action cannot be undone.`,
            'Delete',
            'danger',
            onConfirm
        );
        document.body.appendChild(modal);
        modal.classList.add('show');
    }

    function showRestoreConfirmation(onConfirm) {
        const modal = createConfirmationModal(
            'Restore Database',
            'Are you sure you want to restore the database? This will overwrite all current data and cannot be undone.',
            'Restore',
            'warning',
            onConfirm
        );
        document.body.appendChild(modal);
        modal.classList.add('show');
    }

    function createConfirmationModal(title, message, confirmText, type, onConfirm) {
        const modal = document.createElement('div');
        modal.className = 'modern-confirmation-modal';
        modal.innerHTML = `
            <div class="modern-confirmation-backdrop"></div>
            <div class="modern-confirmation-dialog">
                <div class="confirmation-header">
                    <div class="confirmation-icon ${type}">
                        <i class="bi ${type === 'danger' ? 'bi-exclamation-triangle' : 'bi-question-circle'}"></i>
                    </div>
                    <h4 class="confirmation-title">${title}</h4>
                </div>
                <div class="confirmation-body">
                    <p class="confirmation-message">${message}</p>
                </div>
                <div class="confirmation-actions">
                    <button class="modern-btn-secondary cancel-btn">Cancel</button>
                    <button class="modern-btn-${type} confirm-btn">${confirmText}</button>
                </div>
            </div>
        `;

        // Add styles
        const style = document.createElement('style');
        style.textContent = `
            .modern-confirmation-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            .modern-confirmation-modal.show {
                opacity: 1;
                visibility: visible;
            }
            .modern-confirmation-backdrop {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(8px);
            }
            .modern-confirmation-dialog {
                background: white;
                border-radius: 16px;
                padding: 2rem;
                max-width: 400px;
                width: 90%;
                position: relative;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                transform: scale(0.9);
                transition: transform 0.3s ease;
            }
            .modern-confirmation-modal.show .modern-confirmation-dialog {
                transform: scale(1);
            }
            .confirmation-header {
                text-align: center;
                margin-bottom: 1.5rem;
            }
            .confirmation-icon {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1rem;
                font-size: 1.5rem;
                color: white;
            }
            .confirmation-icon.danger {
                background: linear-gradient(135deg, #ef4444, #dc2626);
            }
            .confirmation-icon.warning {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }
            .confirmation-title {
                margin: 0;
                color: #1f2937;
                font-weight: 600;
            }
            .confirmation-message {
                color: #6b7280;
                text-align: center;
                margin: 0;
                line-height: 1.5;
            }
            .confirmation-actions {
                display: flex;
                gap: 1rem;
                margin-top: 2rem;
            }
            .modern-btn-danger {
                background: linear-gradient(135deg, #ef4444, #dc2626);
                color: white;
                border: none;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                flex: 1;
                transition: all 0.3s ease;
            }
            .modern-btn-warning {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: white;
                border: none;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                flex: 1;
                transition: all 0.3s ease;
            }
            .modern-btn-danger:hover, .modern-btn-warning:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }
        `;
        document.head.appendChild(style);

        // Event listeners
        const cancelBtn = modal.querySelector('.cancel-btn');
        const confirmBtn = modal.querySelector('.confirm-btn');
        const backdrop = modal.querySelector('.modern-confirmation-backdrop');

        const closeModal = () => {
            modal.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(modal);
                document.head.removeChild(style);
            }, 300);
        };

        cancelBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);
        confirmBtn.addEventListener('click', () => {
            closeModal();
            onConfirm();
        });

        return modal;
    }

    // Load backups list on page load
    loadBackupsList();
    
    // Check database status when System Configuration tab is loaded
    checkDatabaseStatus();
});

// Function to check database connection status
function checkDatabaseStatus() {
    const dbStatusElement = document.getElementById('dbStatusValue');
    if (!dbStatusElement) return;
    
    // Show loading state
    dbStatusElement.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Checking...';
    dbStatusElement.className = 'status-value checking';
    
    fetch('../PHP/check_db_status.php')
        .then(response => response.json())
        .then(data => {
            updateDatabaseStatusDisplay(data);
        })
        .catch(error => {
            console.error('Error checking database status:', error);
            updateDatabaseStatusDisplay({
                status: 'disconnected',
                message: 'Failed to check database status',
                timestamp: new Date().toLocaleString()
            });
        });
}

// Function to update database status display
function updateDatabaseStatusDisplay(data) {
    const dbStatusElement = document.getElementById('dbStatusValue');
    if (!dbStatusElement) return;
    
    const isConnected = data.status === 'connected';
    const statusClass = isConnected ? 'connected' : 'disconnected';
    const statusIcon = isConnected ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
    const statusText = isConnected ? 'Connected' : 'Disconnected';
    
    dbStatusElement.innerHTML = `<i class="bi ${statusIcon} me-1"></i>${statusText}`;
    dbStatusElement.className = `status-value ${statusClass}`;
    dbStatusElement.title = `${data.message} (Last checked: ${data.timestamp})`;
}
