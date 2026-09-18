// Utility: Show Toast
function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' ? '✅' : (type === 'error' ? '❌' : 'ℹ️');
    toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideUp 0.3s ease reverse forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Utility: Loader
function showLoader() {
    const loader = document.getElementById('loader');
    if (loader) loader.classList.remove('hidden');
}

function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) loader.classList.add('hidden');
}

// Decode JWT to get user info (simple base64 decode for frontend display)
function getUserInfo() {
    const token = API.getToken();
    if (!token) return null;
    try {
        const payload = token.split('.')[1];
        return JSON.parse(atob(payload));
    } catch (e) {
        return null;
    }
}

function updateUserInfo() {
    const user = getUserInfo();
    const el = document.getElementById('user-email');
    if (user && el) {
        el.textContent = user.data ? user.data.email : 'User';
    }
}

function logout() {
    API.clearToken();
    window.location.href = 'index.html';
}

// Ensure auth logic
document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;
    const isLoginPage = path.endsWith('index.html') || path === '/' || path.endsWith('/frontend/');
    const isThirdParty = path.endsWith('third-party.html');
    
    if (isThirdParty) return; // Handled separately
    
    if (isLoginPage && API.getToken()) {
        window.location.href = 'dashboard.html';
        return;
    }
    
    if (!isLoginPage && !API.getToken()) {
        window.location.href = 'index.html';
        return;
    }

    updateUserInfo();
    
    // Hide initial loader
    setTimeout(hideLoader, 500);

    // Setup logout listener
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) logoutBtn.addEventListener('click', (e) => {
        e.preventDefault();
        logout();
    });

    // Handle Login Page
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            showLoader();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('login-error');
            
            const res = await API.login(email, password);
            if (res.status === 200 && res.token) {
                API.setToken(res.token);
                window.location.href = 'dashboard.html';
            } else {
                hideLoader();
                errorDiv.textContent = res.error || 'Login failed';
                errorDiv.style.color = 'var(--danger)';
            }
        });
    }

    // Handle Dashboard Page
    if (document.getElementById('dashboard-stats')) {
        loadDashboardStats();
    }

    // Handle Trajets Page
    if (document.getElementById('trajets-table')) {
        loadTrajets();
        
        document.getElementById('search-form').addEventListener('submit', (e) => {
            e.preventDefault();
            loadTrajets();
        });
    }

    // Handle Reservations Page
    if (document.getElementById('reservations-table')) {
        loadReservations();
    }
});

async function loadDashboardStats() {
    const res = await API.getStats();
    if (res.status === 200 && res.data) {
        document.getElementById('stat-total').textContent = res.data.total_reservations;
        document.getElementById('stat-confirmed').textContent = res.data.confirmed_reservations;
        document.getElementById('stat-cancelled').textContent = res.data.cancelled_reservations;
    }
    
    // For recent reservations, we just load page 1 and take top 5
    const resList = await API.getReservations(1);
    if (resList.status === 200 && resList.data) {
        const tbody = document.getElementById('recent-reservations');
        tbody.innerHTML = '';
        const recent = resList.data.slice(0, 5);
        
        if (recent.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center">Aucune réservation</td></tr>';
            return;
        }

        recent.forEach(r => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>#${r.id}</td>
                <td>${r.trajet_id} (ID Trajet)</td>
                <td>${r.seats}</td>
                <td><span class="badge badge-${r.status}">${r.status}</span></td>
            `;
            tbody.appendChild(tr);
        });
    }
}

async function loadTrajets() {
    showLoader();
    const depart = document.getElementById('search-depart').value;
    const dest = document.getElementById('search-dest').value;
    const date = document.getElementById('search-date').value;
    
    const params = new URLSearchParams();
    if (depart) params.append('departure', depart);
    if (dest) params.append('destination', dest);
    if (date) params.append('date', date);
    
    const res = await API.getTrajets(params.toString());
    const tbody = document.getElementById('trajets-table').querySelector('tbody');
    tbody.innerHTML = '';
    
    if (res.status === 200 && res.data) {
        if (res.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center">Aucun trajet trouvé</td></tr>';
        } else {
            res.data.forEach(t => {
                const tr = document.createElement('tr');
                const dispo = t.available_seats;
                tr.innerHTML = `
                    <td>${t.departure_city}</td>
                    <td>${t.destination_city}</td>
                    <td>${t.departure_date}</td>
                    <td>${t.departure_time}</td>
                    <td>${t.arrival_time}</td>
                    <td>${t.capacity}</td>
                    <td>${dispo}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="openReserveModal(${t.id}, '${t.departure_city} à ${t.destination_city}', ${dispo})" ${dispo === 0 ? 'disabled' : ''}>
                            Réserver
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } else {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:red">Erreur lors du chargement</td></tr>';
    }
    hideLoader();
}

let selectedTrajetId = null;

function openReserveModal(id, title, maxSeats) {
    selectedTrajetId = id;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('seats-input').max = maxSeats;
    document.getElementById('seats-input').value = 1;
    document.getElementById('reserve-modal').classList.add('active');
}

function closeReserveModal() {
    document.getElementById('reserve-modal').classList.remove('active');
    selectedTrajetId = null;
}

if (document.getElementById('confirm-reserve')) {
    document.getElementById('confirm-reserve').addEventListener('click', async () => {
        const seats = document.getElementById('seats-input').value;
        showLoader();
        const res = await API.createReservation(selectedTrajetId, seats);
        if (res.status === 201) {
            showToast('Réservation créée avec succès !', 'success');
            closeReserveModal();
            loadTrajets(); // reload to update available seats
        } else {
            showToast(res.error || 'Erreur lors de la réservation', 'error');
        }
        hideLoader();
    });
}

if (document.getElementById('close-modal')) {
    document.getElementById('close-modal').addEventListener('click', closeReserveModal);
    document.getElementById('cancel-reserve').addEventListener('click', closeReserveModal);
}

let currentResPage = 1;
async function loadReservations(page = 1) {
    showLoader();
    currentResPage = page;
    const res = await API.getReservations(page);
    const tbody = document.getElementById('reservations-table').querySelector('tbody');
    tbody.innerHTML = '';
    
    if (res.status === 200 && res.data) {
        if (res.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center">Aucune réservation</td></tr>';
        } else {
            res.data.forEach(r => {
                const tr = document.createElement('tr');
               const normalizedStatus = String(r.status || '').toLowerCase();
               const canCancel = !['cancelled', 'annulée'].includes(normalizedStatus);
               tr.innerHTML = `
                   <td>#${r.id}</td>
                   <td>Trajet ${r.trajet_id}</td>
                   <td>${new Date(r.created_at).toLocaleDateString()}</td>
                   <td>${r.seats}</td>
                   <td><span class="badge badge-${normalizedStatus}">${r.status}</span></td>
                   <td>${r.created_at}</td>
                   <td>
                       ${canCancel ? `<button class="btn btn-sm btn-danger" onclick="cancelReservation(${r.id})">Annuler</button>` : '-'}
                   </td>
               `;
                tbody.appendChild(tr);
            });
        }
        
        // Simple pagination info
        const pagination = document.getElementById('pagination-info');
        if (pagination) {
            pagination.innerHTML = `
                <button class="btn btn-sm btn-secondary" onclick="loadReservations(${page - 1})" ${page === 1 ? 'disabled' : ''}>Précédent</button>
                <span>Page ${res.current_page || page}</span>
                <button class="btn btn-sm btn-secondary" onclick="loadReservations(${page + 1})" ${!res.data.length ? 'disabled' : ''}>Suivant</button>
            `;
        }
    }
    hideLoader();
}

async function cancelReservation(id) {
    if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) return;
    
    showLoader();
    const res = await API.cancelReservation(id);
    if (res.status === 200) {
        showToast('Réservation annulée', 'success');
        loadReservations(currentResPage);
    } else {
        showToast(res.error || 'Erreur lors de l\'annulation', 'error');
    }
    hideLoader();
}
