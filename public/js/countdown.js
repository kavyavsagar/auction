function getTimeRemaining(endtime) {
  const total = Date.parse(endtime) - Date.parse(new Date());
 
  const seconds = Math.floor((total / 1000) % 60);
  const minutes = Math.floor((total / 1000 / 60) % 60);
  const hours = Math.floor((total / (1000 * 60 * 60)) % 24);
  const days = Math.floor(total / (1000 * 60 * 60 * 24));
  
  return {
    total,
    days,
    hours,
    minutes,
    seconds
  };
}

function addInterval(d1, mins){
  let d2 = new Date ( d1 );
  d2.setMinutes ( d1.getMinutes() + parseInt(mins) );

  return getFormattedDate(d2);
}

function getFormattedDate(d) {
  let str =  d.getFullYear() + "-" + ('0' + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + d.getDate()).slice(-2) + " " + ('0' + d.getHours()).slice(-2) + ":" + ('0' + d.getMinutes()).slice(-2) + ":" + ('0' + d.getSeconds()).slice(-2);

  return str;
}

function initializeClock(id, endtime) {
  const clock = document.getElementById(id);
  const daysSpan = clock.querySelector('.days');
  const hoursSpan = clock.querySelector('.hours');
  const minutesSpan = clock.querySelector('.minutes');
  const secondsSpan = clock.querySelector('.seconds');

  function updateClock() {
    const t = getTimeRemaining(endtime);
     
    if (t.total < 0) {  
      clearInterval(timeinterval);
      $('.clockdiv').addClass('d-none');

    }else{
      if($('.clockdiv').hasClass('d-none')){
        $('.clockdiv').removeClass('d-none');
      }
      
      daysSpan.innerHTML = t.days;
      hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
      minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
      secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

    }
  }

  //updateClock();
  const timeinterval = setInterval(updateClock, 1000);
}