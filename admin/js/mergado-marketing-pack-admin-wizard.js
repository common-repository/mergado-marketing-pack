(function ($) {
  'use strict';

   document.addEventListener('DOMContentLoaded', function () {
    var $ = jQuery;

    $('[data-mmp-wizard-go]').on('click', async function () {
      var goTo = $(this).attr('data-mmp-wizard-go');
      var feedType = $(this).closest('.wizard').attr('data-mmp-wizard');

      var actionToDo = $(this).attr('data-mmp-wizard-do-before');
      var actionResult = true;
      var $this = this;

      if (actionToDo === 'mmpSaveWpCron') {
        mmpWizard.saveWpCron(feedType);
      } else if (actionToDo === 'mmpSaveWpCronAndGo') {
        var result = mmpWizard.setWizardCompleted(feedType);

        if (result) {
          mmpWizard.saveWpCron(feedType);
          window.location.href = $($this).attr('data-go-to-link');
        }
      } else if (actionToDo === 'setWizardCompletedAndGo') {
        await mmpWizard.setWizardCompleted(feedType).then(function (result) {
          window.location.href = $($this).attr('data-go-to-link');
        });
      } else if (actionToDo === 'mmpGoToLink') {
        actionResult = false;
        window.location.href = $(this).attr('data-go-to-link');
      } else if (actionToDo === 'mmpStartFeedGenerating') {
        mmpWizard.startFeedGenerating(feedType);
      } else if (actionToDo === 'mmpStopProgress') {
        // Because of async dialog
        actionResult = false;
        await mmpWizard.stopProgress(feedType).then(function(result) {
          if (result) {
            mmpWizard.setStepActive(feedType, goTo);
          }
        });
      } else if (actionToDo === 'mmpSkipWizard') {
        // Because of async dialog
        actionResult = false;
        await mmpWizard.skipToFeeds(feedType).then(function(result) {
          if (result) {
            if (mmpWizard.getWizard(feedType).attr('data-forced') == 1) {
              var data = mmpWizard.getFeedData(feedType);
              window.location.href = data.feedListLink;
            } else {
              mmpWizard.setStepActive(feedType, goTo);
            }
          }
        });
      } else if (actionToDo === 'setWizardCompleted') {
        mmpWizard.setWizardCompleted(feedType).then(function (result) {
          // window.location.href = element.attr('data-go-to-link');
        });
      } else if (actionToDo === 'setSelectedWizardAndGenerate') {
          mmpWizard.startFeedGenerating(feedType);
      } else if (actionToDo === 'mmpStartWizardGenerating') {
        mmpWizard.startFeedGenerating(feedType);
      }

      if (actionResult) {
        mmpWizard.setStepActive(feedType, goTo);
      }
    });


     window.mmpWizard = {
       setStepActive: function(feedType, step) {
         mmpWizard.disableAllTabs(feedType);
         var wizard = this.getWizard(feedType);
         wizard.find('[data-mmp-wizard-step="' + step + '"]').addClass('active');
       },
       disableAllTabs: function(feedType) {
         var wizard = this.getWizard(feedType);
         wizard.find('[data-mmp-wizard-step]').removeClass('active');
       }
       ,
       saveWpCron: function(feedType) {
         var tab4a = this.getWizardStep4a(feedType);

         return $.ajax({
           type: "POST",
           url: 'admin-ajax.php',
           data: tab4a.find('.mmp_wizard__wp_cron').serialize(),
           success: function (data, status) {
             if (data) {
               var output = data['data'];
               if (!status || typeof output['error'] !== "undefined") {
                 $('.mmp-popup__output').html(output['error']);
                 return false;
               } else {
                 $('.mmp-popup__output').html(output['success']);
                 return true;
               }
             }
           },
         });
       },
       startFeedGenerating: function(feedType) {
         var data = this.getFeedData(feedType);

         if (!data.frontendData.feedFinished) {
           var tab3 = this.getWizardStep3(feedType);

           this.setVariableFeedRunning(feedType, true)
           this.generateFeed(feedType, true);
           this.setGeneratingButtons(feedType, 'start');

           tab3.find('[data-status]').attr('data-status', 'active');
           tab3.find('.mmp_wizard__generating_svg').addClass('rotating');

           mmpWizardDialog.showDialogAttention();
         }
       },
       setGeneratingButtons: function(feedType, status) {
         var tab3 = mmpWizard.getWizardStep3(feedType);

         if (status === 'start') {
           tab3.find('[data-3-generate]').hide();
           tab3.find('[data-3-generate="skip"]').show();
         } else {
           tab3.find('[data-3-generate]').hide();
           tab3.find('[data-3-generate="done"]').show();
         }
       },
       generateFeed: function(feedType, firstRun = false, force = false) {
         var data = this.getFeedData(feedType);

         if (data.frontendData.feedRunning && !data.frontendData.feedFinished) {

           var formData = this.getWizardStep3(feedType).find('form').serialize();

           if (firstRun) {
             formData = formData + "&firstRun=1";
           }

           if (force) {
             formData = formData + "&force=1";
           }

           $.ajax({
             type: "POST",
             url: 'admin-ajax.php',
             data: formData,
             success: function (data, status) {
               if (data) {
                 var output = data['data'];
                 if (!status || typeof output['error'] !== "undefined") {
                   return false;
                 } else {
                   mmpWizard.fillRangeSlider(feedType, output['percentage'], output['feedStatus']);

                   setTimeout(function () {
                     mmpWizard.generateFeed(feedType);
                   }, 500);

                   if (output['feedStatus'] === 'fullGenerated') {
                     mmpWizard.setFeedFinished(feedType)
                     mmpWizard.setGeneratingButtons(feedType);
                     return true;
                   } else if (output['feedStatus'] === 'merged') {
                     mmpWizard.setFeedFinished(feedType)
                     mmpWizard.setGeneratingButtons(feedType);
                     return true;
                   }
                 }
               }
             },
             error: async function (jqXHR) {
               // Cron failed because "already running"
               if (jqXHR.status === 412) {
                 setTimeout(async function() {
                   await mmpWizardDialog.showDialogAlreadyRunning();
                   await mmpWizard.setWizardCompleted(feedType);

                   if (mmpWizard.getWizardStep3(feedType).closest('.wizard').attr('data-forced') == 1) {
                     window.location.href = data.feedListLink;
                   } else {
                     mmpWizard.setStepActive(feedType, '4a');
                   }
                 }, 2000);

               } else {
                 mmpWizard.lowerProductFeedPerStepAndCallNextRun(feedType);
               }
             }
           });
         }
       },
       lowerProductFeedPerStepAndCallNextRun: function(feedType) {
         var data = this.getFeedData(feedType);

         $.ajax({
           type: "POST",
           url: 'admin-ajax.php',
           data: {
             action: 'ajax_lower_cron_product_step',
             feed: data.feed,
             token: data.token,
             dataType: 'json'
           },
           success: function (output) {
             mmpWizard.setCurrentProductsPerStep(feedType, output['data']['loweredCount']);
             mmpWizard.generateFeed( feedType, false, true);
           },
           error: async function () {
             mmpWizard.setVariableFeedRunning(feedType, false)
             setTimeout(async function() {
               var result = await mmpWizardDialog.showDialogError();

               if(result) {
                 window.location.href = data.feedListLink;
               }
             }, 2000);
           }
         });
       },
       fillRangeSlider: function(feedType, percentage, feedStatus) {
         var element = this.getWizardStep3(feedType);

         if (percentage >= 100 && feedStatus === 'stepGenerated') {
           percentage = 99;
         } else if (feedStatus === 'merged' || feedStatus === 'fullGenerated') {
           percentage = 100;
         }

         if (percentage > 52) {
           element.find('.rangeSliderPercentage').css('color', 'white');
         } else {
           element.find('.rangeSliderPercentage').css('color', 'black');
         }

         this.setProgressStatus(feedType, feedStatus, percentage);

         element.find('.rangeSliderBg').width(percentage + '%');
         element.find('.rangeSliderPercentage').html(percentage + '%');
       },
       setProgressStatus: function (feedType, feedStatus, percentage) {
         var status = 'inactive';

         if (percentage >= 100) {
           status = 'done';
         } else if (percentage > 0) {
           status = 'active';
         }

         if (feedStatus === 'merged' || feedStatus === 'fullGenerated') {
           status = 'done';
         }

         var feedData = this.getFeedData(feedType);

         if (!feedData.frontendData.feedRunning) {
           status = 'inactive';
         }

         this.setWizardGraphic(feedType, status);
       },
       setWizardGraphic(feedType, status)
       {
         var tab3 = this.getWizardStep3(feedType);
         tab3.find('[data-status]').attr('data-status', status);

         if (status === 'active') {
           tab3.find('.mmp_wizard__generating_svg').addClass('rotating');
         } else if (status === 'inactive') {
           tab3.find('.mmp_wizard__generating_svg').removeClass('rotating');
         }
       },
       stopProgress: async function (feedType) {
         var data = this.getFeedData(feedType);

         if (!data.frontendData.feedFinished) {
           var result = await mmpWizardDialog.showDialogLeave();

           if(!result) {
             this.setVariableFeedRunning(feedType, false);
             this.setWizardGraphic(feedType, 'inactive');
             return true;
           } else {
             return false;
           }
         } else {
           return true;
         }
       },
       skipToFeeds: async function (feedType) {
         var data = this.getFeedData(feedType);

         if (!data.frontendData.feedFinished && data.frontendData.feedRunning) {
           var result = await mmpWizardDialog.showDialogLeave();

           if(!result) {
             this.setVariableFeedRunning(feedType, false);
             return true;
           } else {
             return false;
           }
         } else {
           return true;
         }
       },
       setWizardCompleted: async function (feedType) {
         var data = this.getFeedData(feedType);

         return $.ajax({
           type: "POST",
           url: 'admin-ajax.php',
           async: false,
           data: {
             action: 'ajax_set_wizard_complete',
             feed: data.feed,
             token: data.token,
           },
           success: function () {
             return true;
           },
         });
       },
       setFeedFinished: function (feedType) {
         var element = this.getWizardStep3(feedType);

         mmpWizard.setVariableFeedFinished(feedType, true);
         element.find('[data-feed-finished=false]').hide();
         element.find('[data-feed-finished=true]').css('display', 'flex');
       },
       setVariableFeedFinished: function (feedType, value) {
         window.mmpWizardData[feedType]['frontendData']['feedFinished'] = value;
       },
       setVariableFeedRunning: function (feedType, value) {
         window.mmpWizardData[feedType]['frontendData']['feedRunning'] = value;
       },
       setCurrentProductsPerStep: function (feedType, value) {
         window.mmpWizardData[feedType]['frontendData']['itemsPerStep'] = value;
       },
       getFeedData: function (feedType) {
         return window.mmpWizardData[feedType];
       },
       getWizardStep3: function (feedType) {
         return $('[data-mmp-wizard-type="' + feedType + '"][data-mmp-wizard-step="3"]');
       },
       getWizardStep4a: function (feedType) {
         return $('[data-mmp-wizard-type="' + feedType + '"][data-mmp-wizard-step="4a"]');
       },
       getWizardStep4b: function (feedType) {
         return $('[data-mmp-wizard-type="' + feedType + '"][data-mmp-wizard-step="4b"]');
       },
       getWizard: function (feedType) {
         return mmpWizard.getWizardStep3(feedType).closest('.wizard');
       }
     };

     window.mmpWizardDialog = {
         showDialogAlreadyRunning: async function () {
           return await window.yesno(this.getDialogContent('#mmp_dialogAlreadyRunning'));
         },
         showDialogError: async function () {
           return await window.yesno(this.getDialogContent('#mmp_dialogError'));
         },
         showDialogLeave: async function () {
           return await window.yesno(this.getDialogContent('#mmp_dialogLeave'));
         },
         showDialogAttention: async function () {
           setTimeout(async function() {
             return await window.yesno(window.mmpWizardDialog.getDialogContent('#mmp_dialogAttention'));
           }, 1000);
         },
         getDialogContent: function (selector) {
         return {
           labelYes: $(selector).attr('data-mmp-yes'),
           labelNo: $(selector).attr('data-mmp-no'),
           bodyText: $(selector).attr('data-mmp-content')
         }
       }
     };


     // Show leave popup if tab changed
     window.canGo = false;

     $('[data-mmp-tab-button]').on('click', async function (e) {
       if (window.mmpWizardData) {
        var objKeys = Object.keys(window.mmpWizardData);

        if (!window.canGo) {
         for(var index = 0; index < Object.keys(window.mmpWizardData).length; index++) {
           var data = Object.values(window.mmpWizardData)[index].frontendData;

           // Feed is running and is not finnished
           if (data.feedRunning && !data.feedFinished) {
             e.preventDefault();
             e.stopPropagation();
             e.stopImmediatePropagation();

             var result = await window.mmpWizard.stopProgress(objKeys[index]);

             if(result) {
               window.canGo = true;
               $(this).click();
               window.canGo = false;
             } else {
               window.canGo = false;
             }
             break;
           }
         }
        }
       }
     });

     //Show default warning
     $(window).bind('beforeunload', function () {
       if (typeof window.mmpWizardData !== 'undefined') {
         for(var index = 0; index < Object.keys(window.mmpWizardData).length; index++) {
           var data = Object.values(window.mmpWizardData)[index].frontendData;

           // Feed is running and is not finnished
           if (data.feedRunning && !data.feedFinished) {
             return 'Generation is running. Do you really want to leave?';
           }
         }
       }
     });
  });
})(jQuery);
