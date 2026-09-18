document.addEventListener('DOMContentLoaded', () => {
    // Hide loader
    const loader = document.getElementById('loader');
    if (loader) setTimeout(() => loader.classList.add('hidden'), 500);

    const loginSection = document.getElementById('login-section');
    const dashboardSection = document.getElementById('dashboard-section');

    if (ThirdPartyAPI.apiKey) {
        showDashboard();
    } else {
        loginSection.classList.remove('hidden');
    }

    const loginForm = document.getElementById('api-login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const key = document.getElementById('api_key').value;
            ThirdPartyAPI.setApiKey(key);
            
            // Test key
            loader.classList.remove('hidden');
            const res = await ThirdPartyAPI.getStats();
            loader.classList.add('hidden');
            
            if (res.status === 200) {
                showDashboard();
            } else {
                ThirdPartyAPI.clearApiKey();
                const err = document.getElementById('login-error');
                err.textContent = 'API Key invalide';
                err.style.color = 'var(--danger)';
            }
        });
    }

    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            ThirdPartyAPI.clearApiKey();
            window.location.reload();
        });
    }
    
    // Auto refresh toggle
    const toggle = document.getElementById('auto-refresh-toggle');
    let intervalId = null;
    if (toggle) {
        toggle.addEventListener('change', (e) => {
            if (e.target.checked) {
                intervalId = setInterval(loadWebhooks, 5000);
            } else {
                clearInterval(intervalId);
            }
        });
    }
});

function showDashboard() {
    document.getElementById('login-section').style.display = 'none';
    document.getElementById('dashboard-section').classList.remove('hidden');
    document.getElementById('dashboard-section').style.display = 'block';
    
    loadStats();
    loadReservations();
    loadWebhooks();
}

async function loadStats() {
    const res = await ThirdPartyAPI.getStats();
    if (res.status === 200 && res.data) {
        document.getElementById('stat-total').textContent = res.data.total_reservations;
        document.getElementById('stat-confirmed').textContent = res.data.confirmed_reservations;
        document.getElementById('stat-cancelled').textContent = res.data.cancelled_reservations;
    }
}

async function loadReservations() {
    const res = await ThirdPartyAPI.getReservations();
    const tbody = document.getElementById('reservations-table').querySelector('tbody');
    tbody.innerHTML = '';
    
    if (res.status === 200 && res.data) {
        if (res.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center">Aucune réservation</td></tr>';
        } else {
            res.data.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>#${r.id}</td>
                    <td>Trajet ${r.trajet_id}</td>
                    <td>${r.seats}</td>
                    <td><span class="badge badge-${r.status}">${r.status}</span></td>
                    <td>${r.created_at}</td>
                `;
                tbody.appendChild(tr);
            });
        }
    }
}

async function loadWebhooks() {
    const res = await ThirdPartyAPI.getWebhookEvents();
    const tbody = document.getElementById('webhooks-table').querySelector('tbody');
    
    if (res.status === 200 && res.data) {
        tbody.innerHTML = '';
        if (res.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center">Aucun événement</td></tr>';
        } else {
            res.data.forEach(e => {
                const tr = document.createElement('tr');
               const status = String(e.status || '').toLowerCase();
               const statusBadge = ['sent', 'success'].includes(status) ? 'badge-success' : 'badge-failed';
               tr.innerHTML = `
                   <td><strong>${e.event_type}</strong></td>
                   <td><span class="badge ${statusBadge}">${e.status}</span></td>
                   <td>${e.created_at}</td>
                   <td><pre style="font-size:0.75rem; background:rgba(0,0,0,0.2); padding:0.5rem; border-radius:4px; max-height:60px; overflow:auto;">${JSON.stringify(JSON.parse(e.payload || '{}'), null, 2)}</pre></td>
               `;
                tbody.appendChild(tr);
            });
        }
    }
}
