# Twinery activity

This activity allows you to implement single-file .html apps from https://twinery.org/.

To pass grades back to Moodle, your twinery must call

<script>
set backToMoodle to {
    score:7.8,
    type:"twine_result",
    feedback: "You have shown a quite aggressive and self-centered negotiation style"
}
window.parent.postMessage(State.variables.backToMoodle);
</script>

