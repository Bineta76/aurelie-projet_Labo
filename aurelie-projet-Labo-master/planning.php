<?php
session_start();
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Calendrier Médecin - Prise de RDV</title>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    #calendar { max-width: 900px; margin: 0 auto; }
    #rdvForm {
      display: none;
      position: fixed;
      top: 20%;
      left: 50%;
      transform: translateX(-50%);
      background: #f0f0f0;
      padding: 20px;
      border: 1px solid #aaa;
      box-shadow: 0 0 10px #999;
      z-index: 1000;
    }
    #rdvForm input, #rdvForm button {
      margin-top: 10px;
      width: 100%;
      padding: 8px;
    }
    #overlay {
      display: none;
      position: fixed;
      top:0; left:0; right:0; bottom:0;
      background: rgba(0,0,0,0.4);
      z-index: 900;
    }
  </style>
</head>
<body>

  <h1>Calendrier Médecin - Prise de Rendez-vous</h1>
  <div id="calendar"></div>

  <div id="overlay"></div>

  <div id="rdvForm">
    <h3>Nouvel RDV</h3>
    <form id="formRdv">
      <label>Patient :<br><input type="text" id="patientName" required></label><br>
      <label>Date & heure :<br><input type="datetime-local" id="rdvDateTime" required></label><br>
      <button type="submit">Ajouter RDV</button>
      <button type="button" id="cancelBtn">Annuler</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const form = document.getElementById('formRdv');
      const rdvForm = document.getElementById('rdvForm');
      const overlay = document.getElementById('overlay');
      const cancelBtn = document.getElementById('cancelBtn');

      let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        dateClick: function(info) {
          openForm(info.dateStr + "T09:00"); // ouvrir formulaire par défaut à 9h
        },
        eventClick: function(info) {
          if (confirm(`Supprimer le rendez-vous de "${info.event.title}" le ${info.event.start.toLocaleString()} ?`)) {
            info.event.remove();  // supprime l'événement du calendrier
          }
        },
        events: [
          { title: 'Jean Dupont', start: new Date().toISOString().slice(0,10) + 'T10:30' },
          { title: 'Marie Curie', start: new Date().toISOString().slice(0,10) + 'T14:00' }
        ],
      });

      calendar.render();

      function openForm(defaultDateTime) {
        document.getElementById('patientName').value = '';
        document.getElementById('rdvDateTime').value = defaultDateTime;
        rdvForm.style.display = 'block';
        overlay.style.display = 'block';
      }

      function closeForm() {
        rdvForm.style.display = 'none';
        overlay.style.display = 'none';
      }

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const patient = document.getElementById('patientName').value.trim();
        const dateTime = document.getElementById('rdvDateTime').value;

        if(patient && dateTime) {
          calendar.addEvent({
            title: patient,
            start: dateTime,
            allDay: false
          });
          closeForm();
        } else {
          alert("Veuillez remplir tous les champs !");
        }
      });

      cancelBtn.addEventListener('click', closeForm);
      overlay.addEventListener('click', closeForm);
    });
  </script>

</body>
</html>
