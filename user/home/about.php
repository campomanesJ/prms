<?php
include 'header.php';
?>


<body class="hold-transition layout-fixed">
    <div class="wrapper">

    <div class="content">
  <!-- Tabs -->
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="tourist-spot-tab" data-bs-toggle="tab" href="#tourist-spot" role="tab" aria-controls="tourist-spot" aria-selected="false">Tourist Spot</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="workmates-tab" data-bs-toggle="tab" href="#workmates" role="tab" aria-controls="workmates" aria-selected="false">Workmates</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="policy-tab" data-bs-toggle="tab" href="#policy" role="tab" aria-controls="policy" aria-selected="false">Policy</a>
    </li>
  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      <h2>Welcome to Home Tab</h2>
      <p>This is the home section. Here you can find a brief overview of the platform and navigate through various sections. Feel free to explore the features and get started with your tasks!</p>
    </div>
    <div class="tab-pane fade" id="tourist-spot" role="tabpanel" aria-labelledby="tourist-spot-tab">
      <h2>Tourist Spot Information</h2>
      <p>Discover the best tourist spots around the region. Learn about top destinations, travel tips, and how to plan your next adventure. Don’t miss out on these amazing experiences!</p>
    </div>
    <div class="tab-pane fade" id="workmates" role="tabpanel" aria-labelledby="workmates-tab">
      <h2>Workmates Section</h2>
      <p>Meet and collaborate with your colleagues. This section provides a space for team communications, project updates, and work-related discussions.</p>
    </div>
    <div class="tab-pane fade" id="policy" role="tabpanel" aria-labelledby="policy-tab">
      <h2>Policy Information</h2>
      <p>Stay informed about the latest policies and guidelines. This section covers all important administrative policies, compliance rules, and best practices for efficient management.</p>
    </div>
  </div>
</div>




    </div>



    </div>
</body>

<?php
include 'footer.php';
?>