<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    pcd20-platform
 * @category   CategoryName
 */

/**
 * Class m171216_163215_configure_status_validate
 */
class m171216_163215_configure_status_validate extends \yii\db\Migration
{

    /**
     * @throws MigrationsException
     */
    public function safeUp()
    {
        $this->update('sw_workflow', ['initial_status_id' => 'VALIDATED'],
            ['IN', 'id', ['CommunityWorkflow', 'UserProfileWorkflow']]);
        $this->update('sw_workflow', ['initial_status_id' => 'VALIDATO'],
            ['IN', 'id', ['DocumentiWorkflow', 'NewsWorkflow']]);
        $this->update('sw_workflow', ['initial_status_id' => 'ATTIVA'], ['IN', 'id', ['DiscussioniTopicWorkflow']]);
        $this->update('sw_workflow', ['initial_status_id' => 'PUBLISHED'], ['IN', 'id', ['EventWorkflow']]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('sw_workflow', ['initial_status_id' => 'DRAFT'],
            ['IN', 'id', ['CommunityWorkflow', 'UserProfileWorkflow', 'EventWorkflow']]);
        $this->update('sw_workflow', ['initial_status_id' => 'BOZZA'],
            ['IN', 'id', ['DiscussioniTopicWorkflow', 'DocumentiWorkflow', 'NewsWorkflow']]);
        return true;
    }
}