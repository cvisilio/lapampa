<script>
 const recognition = new webkitSpeechRecognition();
    
    const src = "https://audio00.forvo.com/phrases/mp3/c/8/c81c2618ae7246e5148df5892df485a2_9261923_39.mp3";
    
    const transcript = document.getElementById("transcript");
    
    const audio = new Audio();
    
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.onresult = function(event) {
      if (event.results[0].isFinal) {
        transcript.textContent = event.results[0][0].transcript;
        recognition.stop();
      }
    }
    
    recognition.onaudiostart = e => {
      console.log("audio capture started");
    }
    
    recognition.onaudioend = e => {
      console.log("audio capture ended");
    }
    
    audio.oncanplay = () => {
      recognition.start();
      audio.play();
    }
    
    fetch('https://query.yahooapis.com/v1/public/yql?q=select * from data.uri where url="${src}"&format=json&callback=')
      .then(response => response.json())
      .then(({
          query: {
            results: {
              url
            }
          }
        }) =>
        audio.src = url
      )
      .catch(err => console.error(err));
	  
      
     </script>
	  