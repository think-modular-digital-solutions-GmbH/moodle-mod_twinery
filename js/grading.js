window.addEventListener("message", function(event) {

  // Verify the origin of the message.
  const iframe = document.getElementById('twinery_iframe');

  // 1) Must be a real browser-delivered message
  if (!event.isTrusted) return;

  // 2) Must be from our iframe’s window
  if (event.source !== iframe.contentWindow) return;

  let grade = event.data.score;
  let feedback = event.data.feedback;

  if (grade) {
    let url = M.cfg.wwwroot + '/mod/twinery/ajax.php?cmid=' + M.cfg.cmid + '&grade=' + grade + '&feedback=' + feedback + '&sesskey=' + M.cfg.sesskey
    fetch(url, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      credentials: 'same-origin', // send Moodle session cookie
      body: JSON.stringify({
        cmid: M.cfg.cmid,
        grade,
        feedback,
        sesskey: M.cfg.sesskey
      })
    })
      .then(r => r.json())
      .then(res => {
        this.alert(res.message);

        // Reload window, just in case twinery should not be shown any more after attempts are exhausted.
        if (res.status == 'lastattempt') {
          window.location.reload();
        }
      });
  }
});
