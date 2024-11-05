document.addEventListener('DOMContentLoaded', () => {
    let currentWeekOffset = 0;
    generateTimeSlots(currentWeekOffset);
    fetchReservations();
    blockLunchSlots();
    setInterval(fetchReservations, 1000);

    document.getElementById('next-week').addEventListener('click', () => {
        currentWeekOffset += 7;
        generateTimeSlots(currentWeekOffset);
        fetchReservations();
        blockLunchSlots();
        updateWeekDisplay(currentWeekOffset);
    });

    document.getElementById('prev-week').addEventListener('click', () => {
        currentWeekOffset -= 7;
        generateTimeSlots(currentWeekOffset);
        fetchReservations();
        blockLunchSlots();
        updateWeekDisplay(currentWeekOffset);
    });

    updateWeekDisplay(currentWeekOffset);
});

function updateWeekDisplay(weekOffset) {
    const startDate = getStartOfWeekDate(weekOffset);
    const weekDates = [];
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        weekDates.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
    }

    const headerCells = document.querySelectorAll('#calendar-header th');
    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    for (let i = 1; i < headerCells.length; i++) {
        headerCells[i].innerHTML = `${days[i-1]}<br>${weekDates[i-1]}`;
    }
}

function generateTimeSlots(weekOffset) {
    const calendarBody = document.getElementById('calendar-body');
    const startHour = 7;
    const endHour = 17;
    calendarBody.innerHTML = '';

    const now = new Date();
    const currentHour = now.getHours();
    const currentDate = now.toISOString().split('T')[0];

    for (let hour = startHour; hour <= endHour; hour++) {
        const row = document.createElement('tr');
        const timeCell = document.createElement('td');
        timeCell.textContent = `${hour}:00`;
        row.appendChild(timeCell);

        for (let day = 0; day < 7; day++) {
            const cell = document.createElement('td');
            const cellDate = getDateString(day + weekOffset);
            const isPastDate = cellDate < currentDate;
            const isPastTime = cellDate === currentDate && hour < currentHour;

            cell.setAttribute('data-time', `${String(hour).padStart(2, '0')}:00:00`);
            cell.setAttribute('data-date', cellDate);

            if (isPastDate || isPastTime) {
                cell.classList.add('past-date');
               
                cell.onclick = null;
            } else {
                cell.classList.add('available');
                cell.style.backgroundColor = '#4CAF50';
                cell.onclick = () => handleTimeSlotClick(cell);
            }

            row.appendChild(cell);
        }

        calendarBody.appendChild(row);
    }

    updateTimeSelect(startHour, endHour);
}

function updateTimeSelect(startHour, endHour) {
    const timeSelect = document.getElementById('Time');
    timeSelect.innerHTML = '<option value="">Select a time</option>';
    for (let hour = startHour; hour <= endHour; hour++) {
        const timeValue = `${String(hour).padStart(2, '0')}:00:00`;
        timeSelect.innerHTML += `<option value="${timeValue}">${hour}:00</option>`;
    }
}

function handleTimeSlotClick(cell) {
    if (cell.classList.contains('available') && !cell.classList.contains('past-date')) {
        const selectedTime = cell.getAttribute('data-time');
        const selectedDate = cell.getAttribute('data-date');
        document.getElementById('Time').value = selectedTime;
        document.getElementById('Date').value = selectedDate;
    }
}

async function fetchReservations() {
    try {
        const response = await fetch('http://localhost/navigation%20bar/server/availability_data.php');
        const availabilityData = await response.json();

        // Reset all cells to available state
        document.querySelectorAll('td[data-time]').forEach(cell => {
            if (!cell.classList.contains('lunch-time') && !cell.classList.contains('past-date')) {
                cell.classList.remove('reserved', 'partially-reserved');
                cell.classList.add('available');
                
                cell.style.backgroundColor = '#4CAF50';
            }
        });

        availabilityData.forEach(dayData => {
            const { date, reserved_times } = dayData;
            
            if (reserved_times && typeof reserved_times === 'object') {
                Object.entries(reserved_times).forEach(([time, timeData]) => {
                    const cell = document.querySelector(`td[data-date="${date}"][data-time="${time}"]`);
                    if (cell && !cell.classList.contains('past-date')) {
                        cell.classList.remove('available');
                        
                        switch (timeData.status) {
                            case 'red':
                                cell.classList.add('reserved');
                               
                                cell.style.backgroundColor = '#ff4d4d';
                                cell.onclick = null;
                                break;
                            case 'orange':
                                cell.classList.add('partially-reserved');
                               
                                cell.style.backgroundColor = '#FF9800';
                                cell.onclick = () => handleTimeSlotClick(cell);
                                break;
                            case 'green':
                                cell.classList.add('available');
                           
                                cell.style.backgroundColor = '#FF9800';
                                cell.onclick = () => handleTimeSlotClick(cell);
                                break;
                        }
                    }
                });
            }
        });
    } catch (error) {
        console.error('Error fetching reservations:', error);
    }
}

function blockLunchSlots() {
    const lunchCells = document.querySelectorAll('[data-time="12:00:00"]');
    lunchCells.forEach(cell => {
        cell.classList.remove('available');
        cell.classList.add('lunch-time');
        cell.textContent = 'Lunch';
        cell.style.backgroundColor = '#cccccc';
        cell.onclick = null;
    });
}

document.getElementById('reservation-form').addEventListener('submit', async (event) => {
    event.preventDefault();
    const date = document.getElementById('Date').value;
    const time = document.getElementById('Time').value;
    const duration = document.getElementById('Duration').value;

    if (!date || !time || !duration) {
        alert('Please select a valid date, time, and duration.');
        return;
    }

    try {
        const response = await fetch('http://localhost/navigation%20bar/server/availability.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ date, time, duration: parseInt(duration, 10) })
        });

        const result = await response.json();
        if (result.success) {
            alert(result.message);
            fetchReservations();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Error submitting reservation:', error);
        alert('There was an error processing your reservation. Please try again later.');
    }
});

function getStartOfWeekDate(weekOffset = 0) {
    const today = new Date();
    const dayOfWeek = today.getDay();
    const daysToMonday = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - daysToMonday + weekOffset);
    return startOfWeek;
}

function getDateString(dayOffset) {
    const startOfWeek = getStartOfWeekDate();
    const targetDate = new Date(startOfWeek);
    targetDate.setDate(startOfWeek.getDate() + dayOffset);
    return targetDate.toISOString().split('T')[0];
}