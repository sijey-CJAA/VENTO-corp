<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'it_security') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';
require_once '../../../includes/itSecurityHeader.php';
?>

<div class="dashboard mb-5">
    <!-- Header section removed as it's now in itSecurityHeader.php -->

    <div class="row g-4 mb-4">
        <!-- Responsibilities Card -->
        <div class="col-12 col-lg-5">
            <div class="card h-100 p-4 border-0" style="background: linear-gradient(145deg, #18181b, #0f0f11);">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(168, 85, 247, 0.1); color: #a855f7;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <h5 class="text-white fw-bold mb-0">Role Overview</h5>
                        <span class="text-secondary small">IT Security Analyst</span>
                    </div>
                </div>
                <div class="text-secondary small">
                    <p class="mb-3 fw-semibold text-white">Core Responsibilities:</p>
                    <ul class="ps-3 mb-0" style="line-height: 1.8;">
                        <li>Protects the system from unauthorized access</li>
                        <li>Manages user roles and permissions</li>
                        <li>Monitors security threats and suspicious activity</li>
                        <li>Ensures compliance with data privacy policies (e.g., RA 10173)</li>
                        <li>Conducts regular system security checks and audits</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Infrastructure Health & Quick Actions -->
        <div class="col-12 col-lg-7 d-flex flex-column gap-4">
            <!-- Infrastructure Health -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card p-4 border-0 text-center h-100 justify-content-center">
                        <i class="bi bi-server text-success fs-1 mb-2"></i>
                        <h6 class="text-white fw-bold mb-1">Database & Infrastructure</h6>
                        <div><span class="badge bg-success bg-opacity-10 text-success fw-semibold">SECURE & ENCRYPTED</span></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-4 border-0 text-center position-relative overflow-hidden h-100 justify-content-center">
                        <div class="position-absolute w-100 h-100 top-0 start-0" style="background-color: rgba(239, 68, 68, 0.05); z-index: 0;"></div>
                        <i class="bi bi-radar text-danger fs-1 mb-2 position-relative z-1"></i>
                        <h6 class="text-white fw-bold mb-1 position-relative z-1">Active Threats</h6>
                        <div><span class="badge bg-danger bg-opacity-10 text-danger fw-semibold position-relative z-1">2 SUSPICIOUS PINGS</span></div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions (Authentication Management) -->
            <div class="card p-4 border-0 flex-grow-1 justify-content-center">
                <h6 class="text-white fw-bold mb-3">Security Controls</h6>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-outline-dark d-flex align-items-center gap-2 flex-grow-1 justify-content-center py-2">
                        <i class="bi bi-key"></i> Manage Auth
                    </button>
                    <button class="btn btn-outline-dark d-flex align-items-center gap-2 flex-grow-1 justify-content-center py-2">
                        <i class="bi bi-people"></i> User Roles
                    </button>
                    <button class="btn btn-outline-danger d-flex align-items-center gap-2 flex-grow-1 justify-content-center py-2" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2);">
                        <i class="bi bi-exclamation-triangle"></i> Trigger Lockdown
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Threat Detection & Logs -->
    <div class="row g-4">
        <!-- Access Logs Preview -->
        <div class="col-12 col-xl-8">
            <div class="card p-4 border-0 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="text-white fw-bold mb-0">Recent Login & Access Logs</h6>
                    <button class="btn btn-sm btn-outline-dark">View Full Audit</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle" style="--bs-table-bg: transparent;">
                        <thead>
                            <tr>
                                <th class="text-secondary small border-0 px-3 py-2">TIMESTAMP</th>
                                <th class="text-secondary small border-0 px-3 py-2">USER ENTITY</th>
                                <th class="text-secondary small border-0 px-3 py-2">IP ADDRESS</th>
                                <th class="text-secondary small border-0 px-3 py-2">EVENT</th>
                                <th class="text-secondary small border-0 px-3 py-2">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Mock Data -->
                            <tr>
                                <td class="px-3 py-3 border-bottom border-secondary text-secondary small font-monospace">2026-05-14 22:45:12</td>
                                <td class="px-3 py-3 border-bottom border-secondary text-white fw-semibold">J. Doe (Admin)</td>
                                <td class="px-3 py-3 border-bottom border-secondary text-secondary small font-monospace">192.168.1.105</td>
                                <td class="px-3 py-3 border-bottom border-secondary text-secondary small">System Login</td>
                                <td class="px-3 py-3 border-bottom border-secondary">
                                    <span class="badge bg-success bg-opacity-10 text-success">SUCCESS</span>
                                </td>
                            </tr>
                            <tr style="background-color: rgba(239, 68, 68, 0.05);">
                                <td class="px-3 py-3 border-bottom border-secondary text-secondary small font-monospace">2026-05-14 22:30:05</td>
                                <td class="px-3 py-3 border-bottom border-secondary text-white fw-semibold">UNKNOWN</td>
                                <td class="px-3 py-3 border-bottom border-secondary text-danger small font-monospace">45.22.19.8</td>
                                <td class="px-3 py-3 border-bottom border-secondary text-secondary small">Unauthorized Access</td>
                                <td class="px-3 py-3 border-bottom border-secondary">
                                    <span class="badge bg-danger bg-opacity-10 text-danger">BLOCKED</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-3 py-3 border-bottom border-secondary text-secondary small font-monospace">2026-05-14 21:15:44</td>
                                <td class="px-3 py-3 border-bottom border-secondary text-white fw-semibold">T. Smith (HR)</td>
                                <td class="px-3 py-3 border-bottom border-secondary text-secondary small font-monospace">192.168.1.50</td>
                                <td class="px-3 py-3 border-bottom border-secondary text-secondary small">Password Reset</td>
                                <td class="px-3 py-3 border-bottom border-secondary">
                                    <span class="badge bg-success bg-opacity-10 text-success">SUCCESS</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Threat Detection Side Panel -->
        <div class="col-12 col-xl-4">
            <div class="card p-4 border-0 h-100" style="background-color: #121214;">
                <h6 class="text-white fw-bold mb-4">Threat Detection</h6>
                
                <div class="d-flex flex-column gap-3">
                    <div class="p-3 rounded" style="background-color: #18181b; border-left: 3px solid #ef4444;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-white fw-semibold small">Failed Logins Spike</span>
                            <span class="text-danger small fw-bold">HIGH</span>
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 0.75rem;">15 failed attempts detected from IP 45.22.19.8 in the last 10 minutes.</p>
                    </div>

                    <div class="p-3 rounded" style="background-color: #18181b; border-left: 3px solid #eab308;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-white fw-semibold small">Unusual Access Time</span>
                            <span class="text-warning small fw-bold">MODERATE</span>
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 0.75rem;">Account 'm.jones' accessed database outside normal business hours.</p>
                    </div>
                    
                    <button class="btn btn-outline-danger w-100 mt-auto" style="border-color: rgba(239, 68, 68, 0.5); color: #ef4444;">Investigate Alerts</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
