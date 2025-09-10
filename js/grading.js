console.log('Listening for messages from Twinery iframe for grading...');

window.addEventListener("message", function(event) {

  console.log('Received message from Twinery iframe:', event);

  // Verify the origin of the message.
  const iframe = document.getElementById('twinery_iframe');

  // 1) Must be a real browser-delivered message
  if (!event.isTrusted) return;

  // 2) Must be from our iframe’s window
  if (event.source !== iframe.contentWindow) return;

  let grade = event.data.score;
  let feedback = event.data.feedback;

  if (grade) {
    let url = mod_twinery.wwwroot + '/mod/twinery/ajax.php?cmid=' + mod_twinery.cmid + '&grade=' + grade + '&feedback=' + feedback + '&sesskey=' + mod_twinery.sesskey
    fetch(url, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      credentials: 'same-origin', // send Moodle session cookie
      body: JSON.stringify({
        cmid: mod_twinery.cmid,
        grade,
        feedback,
        sesskey: mod_twinery.sesskey
      })
    })
      .then(r => r.json())
      .then(res => {
        // this.alert(res.message);

        // Reload window, just in case twinery should not be shown any more after attempts are exhausted.
        if (res.status == 'lastattempt') {
          window.location.reload();
        }
      });
  }
});
