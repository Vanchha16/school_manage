
    // ---------- Helpers ----------
    const $ = (sel, root=document) => root.querySelector(sel);
    const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

    function toast(msg){
      $("#toastMsg").textContent = msg;
      bootstrap.Toast.getOrCreateInstance($("#toast")).show();
    }

    // ---------- Sidebar (mobile) ----------
    const sidebar = $("#sidebar");
    const backdrop = $("#backdrop");
    $("#btnMenu").addEventListener("click", () => {
      sidebar.classList.add("open");
      backdrop.classList.add("show");
    });
    backdrop.addEventListener("click", () => {
      sidebar.classList.remove("open");
      backdrop.classList.remove("show");
    });

    // ---------- Sidebar nav actions ----------
   
    // ---------- Tabs ----------
    const tabLinks = $$(".tablink");
    const panes = $$(".tabpane");

    function showTab(tab){
      tabLinks.forEach(b => b.classList.toggle("active", b.dataset.tab === tab));
      panes.forEach(p => p.classList.toggle("d-none", p.dataset.tabpane !== tab));
    }

    tabLinks.forEach(b => b.addEventListener("click", () => showTab(b.dataset.tab)));

    // ---------- Topbar actions ----------
    $("#btnBell").addEventListener("click", () => toast("Notifications clicked"));
    $("#btnSettings").addEventListener("click", () => toast("Settings clicked"));
    $("#actionProfile").addEventListener("click", (e) => { e.preventDefault(); toast("Profile clicked"); });
    $("#actionLogout").addEventListener("click", (e) => { e.preventDefault(); toast("Logout clicked"); });

    // ---------- Search ----------
    $("#searchInput").addEventListener("input", (e) => {
      const q = e.target.value.trim().toLowerCase();
      // Example action: filter activity list
      renderActivity(q);
    });

    // ---------- Date range ----------
    function formatRangeLabel(start, end){
      const fmt = (d) => d.toLocaleDateString(undefined, { day:"2-digit", month:"short", year:"numeric" });
      return `${fmt(start)} - ${fmt(end)}`;
    }

    // Default dates
    const now = new Date();
    const startDefault = new Date(now.getFullYear(), 0, 1);
    $("#dateStart").valueAsDate = startDefault;
    $("#dateEnd").valueAsDate = now;
    $("#rangeLabel").textContent = formatRangeLabel(startDefault, now);

    $("#applyRange").addEventListener("click", () => {
      const s = $("#dateStart").valueAsDate;
      const e = $("#dateEnd").valueAsDate;
      if(!s || !e) return toast("Pick both dates");
      if(s > e) return toast("Start date must be before end date");
      $("#rangeLabel").textContent = formatRangeLabel(s, e);
      toast("Date range applied (demo)");
      // Hook: fetch new chart data here
    });

    // ---------- Charts ----------
    // Earnings line chart (solid + dashed)
    const earningsCtx = $("#earningsChart");
    const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

    const earningsChart = new Chart(earningsCtx, {
      type: "line",
      data: {
        labels: months,
        datasets: [
          {
            label: "Earning",
            data: [0, 8, 9, 7, 7, 8, 10, 14, 22, 28, 30, 30],
            tension: 0.4,
            borderWidth: 3,
            pointRadius: 0
          },
          {
            label: "Projection",
            data: [0, 12, 18, 20, 19, 16, 12, 8, 6, 8, 14, 24],
            tension: 0.4,
            borderWidth: 3,
            borderDash: [8, 6],
            pointRadius: 0
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true }
        },
        scales: {
          x: { grid: { display: false } },
          y: {
            beginAtZero: true,
            ticks: {
              callback: (v) => `$${v}k`
            }
          }
        }
      }
    });

    // Attendance doughnut
    const attendanceCtx = $("#attendanceChart");
    let attendance = { present: 65, absent: 35 };

    const attendanceChart = new Chart(attendanceCtx, {
      type: "doughnut",
      data: {
        labels: ["Present", "Absent"],
        datasets: [{
          data: [attendance.present, attendance.absent],
          borderWidth: 0,
          hoverOffset: 6,
          cutout: "70%"
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true }
        }
      }
    });

    function updateAttendanceUI(){
      $("#presentPct").textContent = `${attendance.present}%`;
      $("#absentPct").textContent = `${attendance.absent}%`;
      attendanceChart.data.datasets[0].data = [attendance.present, attendance.absent];
      attendanceChart.update();
    }

    $("#randomizeAttendance").addEventListener("click", () => {
      const present = Math.floor(40 + Math.random() * 56); // 40..95
      attendance.present = present;
      attendance.absent = 100 - present;
      updateAttendanceUI();
      toast("Attendance updated");
    });

    // ---------- Activity list ----------
    const activityData = [
      { icon:"bi-trophy", title:'1st place in "cricket"', sub:"Raj doe won 1st place", time:"Just now" },
      { icon:"bi-bullseye", title:'1st place in "carrom board"', sub:"Raj doe won 1st place", time:"1 day ago" },
      { icon:"bi-lightning-charge", title:'1st place in "running game"', sub:"Raj doe won 1st place", time:"1 day ago" },
      { icon:"bi-award", title:'2nd place in "Netball"', sub:"Raj doe won 1st place", time:"Just now" },
      { icon:"bi-megaphone", title:'3rd place in "speech"', sub:"Raj doe won 1st place", time:"1 day ago" },
      { icon:"bi-shuffle", title:'1st place in "Badminton"', sub:"Raj doe won 1st place", time:"1 day ago" },
    ];

    let activityFiltered = false;

    function renderActivity(query=""){
      const list = $("#activityList");
      list.innerHTML = "";

      let items = [...activityData];

      // Demo "filter" button toggles: show only "Just now"
      if(activityFiltered){
        items = items.filter(x => x.time.toLowerCase().includes("just"));
      }

      if(query){
        items = items.filter(x =>
          (x.title + " " + x.sub + " " + x.time).toLowerCase().includes(query)
        );
      }

      if(items.length === 0){
        list.innerHTML = `<div class="text-muted small">No activity found.</div>`;
        return;
      }

      items.forEach(item => {
        const el = document.createElement("div");
        el.className = "activity-item";
        el.innerHTML = `
          <div class="activity-icon"><i class="bi ${item.icon}"></i></div>
          <div>
            <div class="activity-title">${item.title}</div>
            <div class="activity-sub">${item.sub}</div>
          </div>
          <div class="activity-time">${item.time}</div>
        `;
        el.addEventListener("click", () => toast(`Opened: ${item.title}`));
        list.appendChild(el);
      });
    }

    $("#filterActivity").addEventListener("click", () => {
      activityFiltered = !activityFiltered;
      renderActivity($("#searchInput").value.trim().toLowerCase());
      toast(activityFiltered ? "Filter: Just now" : "Filter cleared");
    });

    renderActivity();
