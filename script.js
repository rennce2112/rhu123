
function generateTimeSlots() {
  const calendarBody = document.getElementById('calendar-body');
  const startHour = 9; 
  const endHour = 17;   

  for (let hour = startHour; hour <= endHour; hour++) {
      // Create a new row for each hour
      const row = document.createElement('tr');
      const timeCell = document.createElement('td');
      timeCell.textContent = `${hour}:00`;  // Set the time

      row.appendChild(timeCell);

      
      for (let day = 0; day < 7; day++) {
          const cell = document.createElement('td');
          cell.classList.add('available');
          cell.setAttribute('data-time', `${hour}:00`); 
          cell.setAttribute('data-date', getDateString(day));
          cell.onclick = () => handleTimeSlotClick(cell); 
          row.appendChild(cell);
      }

      calendarBody.appendChild(row);
  }
}
function handleTimeSlotClick(cell) {
    if (cell.classList.contains('available')) {
      const selectedTime = cell.getAttribute('data-time');
      const selectedDate = cell.getAttribute('data-date');
  
      document.getElementById('Time').value = selectedTime;
      document.getElementById('Date').value = selectedDate;
  
      // Optionally mark the cell as reserved
      cell.classList.remove('available');
      cell.classList.add('reserved');
    } else {
      alert('This time slot is already reserved.');
    }
  }
  


