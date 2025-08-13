console.log("Listening to grading events from iframe...");

window.addEventListener("message", function(event) {

  // Optionally verify the origin for security
  // if (event.origin !== "https://trusted-iframe-origin.com") {
  //   console.warn("Blocked message from unknown origin:", event.origin);
  //   return;
  // }

  let grade = event.data.score;
  let feedback = event.data.feedback;
  console.log("Received grade:", grade, "with feedback:", feedback);
  if (grade) {
    console.log("Sending grade to server...");
    let url = M.cfg.wwwroot + '/mod/twinery/ajax.php?cmid=' + M.cfg.cmid + '&grade=' + grade + '&feedback=' + feedback + '&sesskey=' + M.cfg.sesskey
    console.log("Grade URL:", url);
    fetch(url)
      .then(r => r.json())
      .then(res => {
        console.log('Grade response:', res);
        this.alert(res.message);

        // Reload window, just in case twinery should not be shown any more after attempts are exhausted.
        if (res.status == 'lastattempt') {
          window.location.reload();
        }
      });
  }
});
