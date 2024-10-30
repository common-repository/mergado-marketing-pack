<script src="https://c.seznam.cz/js/rc.js"></script>

<script>
  if (typeof identities === 'undefined') {
      let identities = {};

      <?php if($email !== null) { ?>
      identities.eid = "<?php echo $email; ?>";
      <?php } ?>

      <?php if($phone !== null) { ?>
      identities.tid = "<?php echo $phone; ?>";
      <?php } ?>

      window.sznIVA.IS.updateIdentities(identities);
  }
</script>
