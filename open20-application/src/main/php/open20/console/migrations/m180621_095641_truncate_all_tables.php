<?php

use yii\db\Migration;
use yii\helpers\Console;

class m180621_095641_truncate_all_tables extends Migration
{
    public function safeUp()
    {
          try{
              $console = new Console();
              $console->output('Begin clear operation....');
                $this->execute('SET FOREIGN_KEY_CHECKS=0;');
              
                $this->execute('truncate table audit_data;');
                $this->execute('truncate table audit_entry;');
                $this->execute('truncate table audit_error;');
                $this->execute('truncate table audit_javascript;');
                $this->execute('truncate table audit_mail;');
                $this->execute('truncate table audit_trail;');
                $this->execute('truncate table cwh_pubblicazioni;');
                $this->execute('truncate table cwh_pubblicazioni_cwh_nodi_editori_mm;');
                $this->execute('truncate table cwh_pubblicazioni_cwh_nodi_validatori_mm;');
                $this->execute('truncate table documenti;');
                $this->execute('truncate table documenti_allegati;');
                $this->execute('truncate table documenti_notifiche_preferenze;');
                $this->execute('truncate table email_spool;');
                $this->execute('truncate table community;');
                $this->execute('truncate table community_user_mm;');
                $this->execute('truncate table event;');
                $this->execute('truncate table groups;');
                $this->execute('truncate table groups_members;');
                $this->execute('truncate table news;');
                $this->execute('truncate table notification;');
                $this->execute('truncate table notificationread;');
                $this->execute('truncate table amos_workflow_transitions_log;');
                $this->execute('truncate table attach_file;');
                $this->execute('truncate table attach_file_refs;');
                $this->execute('truncate table discussioni_topic;');
                $this->execute('truncate table comment;');
                $this->execute('truncate table comment_reply;');
                $this->execute('truncate table report;');
                $this->execute('truncate table statistics_attachments;');
                $this->execute('truncate table cwh_nodi_mt;');
                $this->execute('insert into cwh_nodi_mt select * from cwh_nodi_view;');
                
                $this->execute('SET FOREIGN_KEY_CHECKS=1;');
             $console->output('End clear operation.');
          } catch (Exception $ex) {
               Console.output($ex->getMessage());
          }
    }

    public function safeDown()
    {
        echo "m180621_095641_truncate_all_tables cannot be reverted.\n";

        return false;
    }

    
}
